<?php

namespace App\Http\Controllers;

use App\Models\KelompokTransferPemasok;
use Illuminate\Support\Facades\DB;

// controller untuk transfer pemasok
// yang mengelola daftar, tambah, ubah, hapus, dan detail transfer pemasok.
class TrtrfpController extends Controller
{
    public function index(array $data)
    {
        $data['transfers'] = KelompokTransferPemasok::query()->join('rekening_bank_perusahaan as rb', 'rb.id', '=', 'kelompok_transfer_pemasok.rekening_bank_perusahaan_id')
            ->select('kelompok_transfer_pemasok.*', 'rb.nama_bank as bank_sumber')->latest('tanggal_transfer')->get();

        return view('bayarp.trtrfp.list', $data);
    }

    public function add(array $data)
    {
        return view('bayarp.trtrfp.add', $this->formData($data));
    }

    public function edit(array $data)
    {
        $transfer = KelompokTransferPemasok::findOrFail(decrypt($data['idencrypt']));
        abort_unless($transfer->status === 'draf', 422, 'Hanya transfer berstatus draf yang dapat diubah.');

        return view('bayarp.trtrfp.edit', $this->formData($data, $transfer));
    }

    public function show(array $data)
    {
        $data['transfer'] = KelompokTransferPemasok::findOrFail(decrypt($data['idencrypt']));
        $data['items'] = $data['transfer']->items()->join('rincian_perintah_pembayaran as rpp', 'rpp.id', '=', 'rincian_transfer_pemasok.rincian_perintah_pembayaran_id')
            ->join('faktur_pembelian as fp', 'fp.id', '=', 'rpp.faktur_pembelian_id')->join('rekening_bank_pemasok as rb', 'rb.id', '=', 'rincian_transfer_pemasok.rekening_bank_pemasok_id')
            ->select('rincian_transfer_pemasok.*', 'fp.nomor_form', 'rb.nama_bank', 'rb.nomor_rekening')->get();

        return view('bayarp.trtrfp.show', $data);
    }

    public function store(array $data)
    {
        DB::transaction(fn () => $this->save());

        return redirect('trtrfp')->with(['message' => 'Transfer pemasok berhasil disimpan.', 'class' => 'success']);
    }

    public function update(array $data)
    {
        $transfer = KelompokTransferPemasok::findOrFail(decrypt($data['idencrypt']));
        abort_unless($transfer->status === 'draf', 422, 'Hanya transfer berstatus draf yang dapat diubah.');
        DB::transaction(fn () => $this->save($transfer));

        return redirect('trtrfp')->with(['message' => 'Transfer pemasok berhasil diperbarui.', 'class' => 'success']);
    }

    public function destroy(array $data)
    {
        $transfer = KelompokTransferPemasok::findOrFail(decrypt($data['idencrypt']));
        abort_unless($transfer->status === 'draf', 422, 'Transfer yang diproses tidak dapat dibatalkan.');
        $transfer->update(['status' => 'gagal', 'user_update' => session('username')]);

        return redirect('trtrfp')->with(['message' => 'Transfer pemasok dibatalkan.', 'class' => 'success']);
    }

    private function formData(array $data, ?KelompokTransferPemasok $transfer = null): array
    {
        $data['transfer'] = $transfer;
        $data['items'] = $transfer?->items ?? collect();
        $data['companyBanks'] = DB::table('rekening_bank_perusahaan')->orderBy('nama_bank')->get();
        $data['supplierBanks'] = DB::table('rekening_bank_pemasok')->orderBy('nama_bank')->get();
        $data['transactions'] = DB::table('rincian_perintah_pembayaran as rpp')->join('perintah_pembayaran as pp', 'pp.id', '=', 'rpp.perintah_pembayaran_id')
            ->join('faktur_pembelian as fp', 'fp.id', '=', 'rpp.faktur_pembelian_id')->where('pp.status', '!=', 'dibatalkan')
            ->select('rpp.id', 'pp.nomor_bukti', 'fp.nomor_form', 'rpp.jumlah_dialokasikan')->get();

        return $data;
    }

    private function save(?KelompokTransferPemasok $transfer = null): void
    {
        $input = request()->validate([
            'rekening_bank_perusahaan_id' => ['required', 'exists:rekening_bank_perusahaan,id'], 'tanggal_transfer' => ['required', 'date'],
            'items' => ['required', 'array', 'min:1'], 'items.*.rincian_perintah_pembayaran_id' => ['required', 'distinct', 'exists:rincian_perintah_pembayaran,id'],
            'items.*.rekening_bank_pemasok_id' => ['required', 'exists:rekening_bank_pemasok,id'], 'items.*.jumlah_transfer' => ['required', 'numeric', 'gt:0'], 'items.*.nomor_referensi' => ['nullable', 'string', 'max:100'],
        ]);
        $total = round(collect($input['items'])->sum('jumlah_transfer'), 2);
        $payload = collect($input)->except('items')->all() + ['total_transfer' => $total, 'user_update' => session('username')];
        $transfer ??= KelompokTransferPemasok::create($payload + ['user_create' => session('username')]);
        if ($transfer->wasRecentlyCreated === false) {
            $transfer->update($payload);
            $transfer->items()->delete();
        }
        foreach ($input['items'] as $item) {
            $transaction = DB::table('rincian_perintah_pembayaran as rpp')->join('faktur_pembelian as fp', 'fp.id', '=', 'rpp.faktur_pembelian_id')
                ->where('rpp.id', $item['rincian_perintah_pembayaran_id'])->select('rpp.jumlah_dialokasikan', 'fp.pemasok_id')->firstOrFail();
            abort_if((float) $item['jumlah_transfer'] > (float) $transaction->jumlah_dialokasikan, 422, 'Nilai transfer melebihi nilai perintah pembayaran.');
            abort_unless(DB::table('rekening_bank_pemasok')->where('id', $item['rekening_bank_pemasok_id'])->where('pemasok_id', $transaction->pemasok_id)->exists(), 422, 'Rekening tujuan harus milik pemasok pada transaksi.');
            $transfer->items()->create($item + ['status' => 'menunggu']);
        }
    }
}
