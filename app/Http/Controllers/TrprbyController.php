<?php

namespace App\Http\Controllers;

use App\Models\FakturPembelian;
use App\Models\PerintahPembayaran;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

// controller untuk perintah pembayaran
// yang mengelola daftar, tambah, ubah, hapus, dan detail perintah pembayaran.
class TrprbyController extends Controller
{
    public function index(array $data)
    {
        $data['orders'] = PerintahPembayaran::latest('tanggal_batas_transfer')->get();

        return view('bayarp.trprby.list', $data);
    }

    public function add(array $data)
    {
        return view('bayarp.trprby.add', $this->formData($data));
    }

    public function edit(array $data)
    {
        $order = PerintahPembayaran::findOrFail(decrypt($data['idencrypt']));
        abort_unless($order->status === 'draf', 422, 'Hanya perintah pembayaran berstatus draf yang dapat diubah.');

        return view('bayarp.trprby.edit', $this->formData($data, $order));
    }

    public function show(array $data)
    {
        $data['order'] = PerintahPembayaran::findOrFail(decrypt($data['idencrypt']));
        $data['items'] = $data['order']->items()->join('faktur_pembelian as fp', 'fp.id', '=', 'rincian_perintah_pembayaran.faktur_pembelian_id')
            ->join('pemasok as p', 'p.id', '=', 'fp.pemasok_id')->select('rincian_perintah_pembayaran.*', 'fp.nomor_form', 'p.nama as pemasok')->get();

        return view('bayarp.trprby.show', $data);
    }

    public function store(array $data)
    {
        DB::transaction(fn () => $this->save());

        return redirect('trprby')->with(['message' => 'Perintah pembayaran berhasil disimpan.', 'class' => 'success']);
    }

    public function update(array $data)
    {
        $order = PerintahPembayaran::findOrFail(decrypt($data['idencrypt']));
        abort_unless($order->status === 'draf', 422, 'Hanya perintah pembayaran berstatus draf yang dapat diubah.');
        DB::transaction(fn () => $this->save($order));

        return redirect('trprby')->with(['message' => 'Perintah pembayaran berhasil diperbarui.', 'class' => 'success']);
    }

    public function destroy(array $data)
    {
        $order = PerintahPembayaran::findOrFail(decrypt($data['idencrypt']));
        abort_unless($order->status === 'draf', 422, 'Perintah pembayaran yang diproses tidak dapat dibatalkan.');
        $order->update(['status' => 'dibatalkan', 'user_update' => session('username')]);

        return redirect('trprby')->with(['message' => 'Perintah pembayaran dibatalkan.', 'class' => 'success']);
    }

    private function formData(array $data, ?PerintahPembayaran $order = null): array
    {
        $data['order'] = $order;
        $data['items'] = $order?->items ?? collect();
        $data['invoices'] = FakturPembelian::where('status', '!=', 'dibatalkan')->where('jumlah_terutang', '>', 0)->get(['id', 'pemasok_id', 'nomor_form', 'tanggal_faktur', 'total_akhir', 'jumlah_terutang']);
        $data['supplierBanks'] = DB::table('rekening_bank_pemasok')->orderBy('nama_bank')->get();

        return $data;
    }

    private function save(?PerintahPembayaran $order = null): void
    {
        $input = request()->validate([
            'nomor_bukti' => ['required', 'string', 'max:50', Rule::unique('perintah_pembayaran', 'nomor_bukti')->ignore($order?->id)],
            'tanggal_batas_transfer' => ['required', 'date'],
            'metode_bayar' => ['required', Rule::in(['tunai', 'cek_giro', 'transfer_bank', 'edc', 'kartu_debit', 'kartu_kredit', 'qris', 'tautan_pembayaran', 'virtual_account', 'dompet_digital', 'non_tunai_lainnya'])],
            'keterangan' => ['nullable', 'string'], 'items' => ['required', 'array', 'min:1'],
            'items.*.faktur_pembelian_id' => ['required', 'distinct', 'exists:faktur_pembelian,id'], 'items.*.rekening_bank_pemasok_id' => ['nullable', 'exists:rekening_bank_pemasok,id'],
            'items.*.jumlah_bayar' => ['required', 'numeric', 'gt:0'], 'items.*.jumlah_diskon' => ['nullable', 'numeric', 'min:0'],
        ]);
        $payload = collect($input)->except('items')->all() + ['user_update' => session('username')];
        $order ??= PerintahPembayaran::create($payload + ['user_create' => session('username')]);
        if ($order->wasRecentlyCreated === false) {
            $order->update($payload);
            $order->items()->delete();
        }
        foreach ($input['items'] as $item) {
            $invoice = FakturPembelian::findOrFail($item['faktur_pembelian_id']);
            $paid = round((float) $item['jumlah_bayar'] - (float) ($item['jumlah_diskon'] ?? 0), 2);
            abort_if($paid <= 0 || $paid > (float) $invoice->jumlah_terutang, 422, 'Nilai bayar melebihi nilai terutang faktur.');
            if (! empty($item['rekening_bank_pemasok_id'])) {
                abort_unless(DB::table('rekening_bank_pemasok')->where('id', $item['rekening_bank_pemasok_id'])->where('pemasok_id', $invoice->pemasok_id)->exists(), 422, 'Rekening bank harus milik pemasok pada faktur.');
            }
            $order->items()->create(['faktur_pembelian_id' => $invoice->id, 'rekening_bank_pemasok_id' => $item['rekening_bank_pemasok_id'] ?? null, 'total_faktur' => $invoice->total_akhir, 'jumlah_terutang' => $invoice->jumlah_terutang, 'jumlah_bayar' => $item['jumlah_bayar'], 'jumlah_diskon' => $item['jumlah_diskon'] ?? 0, 'jumlah_dialokasikan' => $paid]);
        }
    }
}
