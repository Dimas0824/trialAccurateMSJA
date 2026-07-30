<?php

namespace App\Http\Controllers;

use App\Models\FakturPembelian;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

// controller untuk faktur pembelian
// yang mengelola daftar, tambah, ubah, hapus, dan detail faktur pembelian.
class TrfkbController extends Controller
{
    public function index(array $data)
    {
        $data['invoices'] = FakturPembelian::query()->join('pemasok', 'pemasok.id', '=', 'faktur_pembelian.pemasok_id')
            ->select('faktur_pembelian.*', 'pemasok.nama as pemasok')->latest('tanggal_faktur')->get();

        return view('tertag.trfkb.list', $data);
    }

    public function add(array $data)
    {
        return view('tertag.trfkb.add', $this->formData($data));
    }

    public function edit(array $data)
    {
        $invoice = $this->invoice(decrypt($data['idencrypt']));
        abort_unless($invoice->status === 'draf', 422, 'Hanya faktur berstatus draf yang dapat diubah.');

        return view('tertag.trfkb.edit', $this->formData($data, $invoice));
    }

    public function show(array $data)
    {
        $data['invoice'] = $this->invoice(decrypt($data['idencrypt']));
        $data['items'] = $data['invoice']->items;
        $data['supplierName'] = DB::table('pemasok')->where('id', $data['invoice']->pemasok_id)->value('nama');

        return view('tertag.trfkb.show', $data);
    }

    public function store(array $data)
    {
        DB::transaction(fn () => $this->save());

        return redirect('trfkb')->with(['message' => 'Faktur pembelian berhasil disimpan.', 'class' => 'success']);
    }

    public function update(array $data)
    {
        $invoice = $this->invoice(decrypt($data['idencrypt']));
        abort_unless($invoice->status === 'draf', 422, 'Hanya faktur berstatus draf yang dapat diubah.');
        DB::transaction(fn () => $this->save($invoice));

        return redirect('trfkb')->with(['message' => 'Faktur pembelian berhasil diperbarui.', 'class' => 'success']);
    }

    public function destroy(array $data)
    {
        $invoice = $this->invoice(decrypt($data['idencrypt']));
        abort_unless($invoice->status === 'draf', 422, 'Faktur yang sudah diproses tidak dapat dibatalkan.');
        $invoice->update(['status' => 'dibatalkan', 'user_update' => session('username')]);

        return redirect('trfkb')->with(['message' => 'Faktur pembelian dibatalkan.', 'class' => 'success']);
    }

    private function formData(array $data, ?FakturPembelian $invoice = null): array
    {
        $data['invoice'] = $invoice;
        $data['items'] = $invoice?->items ?? collect();
        $data['suppliers'] = DB::table('pemasok')->where('isactive', '1')->orderBy('nama')->get();
        $data['goods'] = DB::table('barang_jasa')->where('isactive', '1')->orderBy('nama_barang')->get(['kode_barang', 'nama_barang', 'satuan']);

        return $data;
    }

    private function invoice(int $id): FakturPembelian
    {
        return FakturPembelian::findOrFail($id);
    }

    private function save(?FakturPembelian $invoice = null): void
    {
        $input = request()->validate([
            'pemasok_id' => ['required', 'exists:pemasok,id'],
            'nomor_form' => ['required', 'string', 'max:50', Rule::unique('faktur_pembelian', 'nomor_form')->ignore($invoice?->id)],
            'nomor_faktur_pemasok' => ['required', 'string', 'max:50'],
            'tanggal_faktur' => ['required', 'date'],
            'keterangan' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.barang_jasa_kode' => ['required', 'distinct', 'exists:barang_jasa,kode_barang'],
            'items.*.kuantitas' => ['required', 'numeric', 'gt:0'],
            'items.*.harga_satuan' => ['required', 'numeric', 'min:0'],
            'items.*.jumlah_diskon' => ['nullable', 'numeric', 'min:0'],
        ]);
        $goods = DB::table('barang_jasa')->whereIn('kode_barang', collect($input['items'])->pluck('barang_jasa_kode'))->get()->keyBy('kode_barang');
        $lines = collect($input['items'])->map(function (array $item) use ($goods) {
            $good = $goods[$item['barang_jasa_kode']];
            $quantity = (float) $item['kuantitas'];
            $price = (float) $item['harga_satuan'];
            $discount = (float) ($item['jumlah_diskon'] ?? 0);

            return ['barang_jasa_kode' => $good->kode_barang, 'nama_barang_snapshot' => $good->nama_barang, 'satuan_snapshot' => $good->satuan, 'kuantitas' => $quantity, 'harga_satuan' => $price, 'jumlah_diskon' => $discount, 'total_baris' => round(($quantity * $price) - $discount, 2)];
        });
        $total = round($lines->sum('total_baris'), 2);
        $payload = collect($input)->except('items')->all() + ['subtotal' => $total, 'total_akhir' => $total, 'jumlah_terutang' => $total, 'user_update' => session('username')];

        // Rincian draf diganti utuh karena belum menjadi dasar pembayaran.
        $invoice ??= FakturPembelian::create($payload + ['user_create' => session('username')]);
        if ($invoice->wasRecentlyCreated === false) {
            $invoice->update($payload);
            $invoice->items()->delete();
        }
        $invoice->items()->createMany($lines->all());
    }
}
