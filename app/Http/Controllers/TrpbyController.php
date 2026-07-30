<?php

namespace App\Http\Controllers;

use App\Models\FakturPembelian;
use App\Models\PembayaranPembelian;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

// controller untuk pembayaran pembelian
// yang mengelola daftar, tambah, hapus, dan detail pembayaran pembelian.
class TrpbyController extends Controller
{
    public function index(array $data)
    {
        $data['payments'] = PembayaranPembelian::query()->join('pemasok', 'pemasok.id', '=', 'pembayaran_pembelian.pemasok_id')
            ->select('pembayaran_pembelian.*', 'pemasok.nama as pemasok')->latest('tanggal_pembayaran')->get();

        return view('bayarp.trpby.list', $data);
    }

    public function add(array $data)
    {
        return view('bayarp.trpby.add', $this->formData($data));
    }

    public function edit(array $data)
    {
        abort(422, 'Pembayaran yang sudah disimpan tidak dapat diubah; batalkan lalu buat dokumen baru.');
    }

    public function show(array $data)
    {
        $data['payment'] = PembayaranPembelian::findOrFail(decrypt($data['idencrypt']));
        $data['items'] = $data['payment']->allocations()->join('faktur_pembelian', 'faktur_pembelian.id', '=', 'alokasi_pembayaran_faktur.faktur_pembelian_id')
            ->select('alokasi_pembayaran_faktur.*', 'faktur_pembelian.nomor_form')->get();

        return view('bayarp.trpby.show', $data);
    }

    public function store(array $data)
    {
        DB::transaction(fn () => $this->save());

        return redirect('trpby')->with(['message' => 'Pembayaran pembelian berhasil disimpan.', 'class' => 'success']);
    }

    public function update(array $data)
    {
        return $this->edit($data);
    }

    public function destroy(array $data)
    {
        DB::transaction(function () use ($data) {
            $payment = PembayaranPembelian::lockForUpdate()->findOrFail(decrypt($data['idencrypt']));
            abort_unless($payment->status === 'diposting', 422, 'Hanya pembayaran yang telah diposting dapat dibatalkan.');
            foreach ($payment->allocations as $allocation) {
                $invoice = FakturPembelian::lockForUpdate()->findOrFail($allocation->faktur_pembelian_id);
                $outstanding = round((float) $invoice->jumlah_terutang + (float) $allocation->jumlah_dialokasikan, 2);
                $invoice->update(['jumlah_terutang' => $outstanding, 'status' => $outstanding >= (float) $invoice->total_akhir ? 'terutang' : 'dibayar_sebagian', 'user_update' => session('username')]);
            }
            $payment->update(['status' => 'dibatalkan', 'user_update' => session('username')]);
        });

        return redirect('trpby')->with(['message' => 'Pembayaran pembelian dibatalkan.', 'class' => 'success']);
    }

    private function formData(array $data): array
    {
        $data['suppliers'] = DB::table('pemasok')->where('isactive', '1')->orderBy('nama')->get();
        $data['banks'] = DB::table('akun_perkiraan')->where('isactive', '1')->orderBy('nama_akun')->get();
        $data['invoices'] = FakturPembelian::where('status', '!=', 'dibatalkan')->where('jumlah_terutang', '>', 0)->get(['id', 'pemasok_id', 'nomor_form', 'tanggal_faktur', 'total_akhir', 'jumlah_terutang']);

        return $data;
    }

    private function save(): void
    {
        $input = request()->validate([
            'pemasok_id' => ['required', 'exists:pemasok,id'], 'akun_bank_id' => ['required', 'exists:akun_perkiraan,id'],
            'nomor_bukti' => ['required', 'string', 'max:50', Rule::unique('pembayaran_pembelian', 'nomor_bukti')], 'tanggal_pembayaran' => ['required', 'date'],
            'metode_bayar' => ['required', Rule::in(['tunai', 'cek_giro', 'transfer_bank', 'edc', 'kartu_debit', 'kartu_kredit', 'qris', 'tautan_pembayaran', 'virtual_account', 'dompet_digital', 'non_tunai_lainnya'])],
            'keterangan' => ['nullable', 'string'], 'items' => ['required', 'array', 'min:1'],
            'items.*.faktur_pembelian_id' => ['required', 'distinct', 'exists:faktur_pembelian,id'], 'items.*.jumlah_bayar' => ['required', 'numeric', 'gt:0'], 'items.*.jumlah_diskon' => ['nullable', 'numeric', 'min:0'],
        ]);
        $payment = PembayaranPembelian::create(collect($input)->except('items')->all() + ['nilai_pembayaran' => 0, 'status' => 'diposting', 'user_create' => session('username'), 'user_update' => session('username')]);
        $total = 0.0;
        foreach ($input['items'] as $item) {
            $invoice = FakturPembelian::lockForUpdate()->findOrFail($item['faktur_pembelian_id']);
            abort_unless((int) $invoice->pemasok_id === (int) $input['pemasok_id'], 422, 'Faktur harus milik pemasok yang dipilih.');
            $paid = round((float) $item['jumlah_bayar'] - (float) ($item['jumlah_diskon'] ?? 0), 2);
            abort_if($paid <= 0 || $paid > (float) $invoice->jumlah_terutang, 422, 'Nilai bayar melebihi nilai terutang faktur.');
            $outstanding = round((float) $invoice->jumlah_terutang - $paid, 2);
            $payment->allocations()->create(['faktur_pembelian_id' => $invoice->id, 'total_faktur' => $invoice->total_akhir, 'jumlah_terutang' => $invoice->jumlah_terutang, 'jumlah_bayar' => $item['jumlah_bayar'], 'jumlah_diskon' => $item['jumlah_diskon'] ?? 0, 'jumlah_dialokasikan' => $paid]);
            $invoice->update(['jumlah_terutang' => $outstanding, 'status' => $outstanding <= 0 ? 'lunas' : 'dibayar_sebagian', 'user_update' => session('username')]);
            $total += $paid;
        }
        $payment->update(['nilai_pembayaran' => round($total, 2)]);
    }
}
