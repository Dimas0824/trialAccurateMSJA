<?php

namespace App\Http\Controllers;

use App\Models\PenerimaanBarang;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

// controller untuk transaksi penerimaan barang (TRPNB)
// yang mengelola daftar, tambah, ubah, hapus, dan detail penerimaan barang.
class TrpnbController extends Controller
{
    public function index(array $data)
    {
        $data['receipts'] = PenerimaanBarang::query()
            ->join('pemasok as p', 'p.id', '=', 'penerimaan_barang.pemasok_id')
            ->select('penerimaan_barang.*', 'p.nama as pemasok')
            ->latest('penerimaan_barang.tanggal_penerimaan')->get();

        return view('tertag.trpnb.list', $data);
    }

    public function add(array $data)
    {
        return view('tertag.trpnb.add', $this->formData($data));
    }

    public function edit(array $data)
    {
        $receipt = $this->receipt(decrypt($data['idencrypt']));
        abort_unless($receipt->status === 'draf', 422, 'Hanya penerimaan berstatus draf yang dapat diubah.');

        return view('tertag.trpnb.edit', $this->formData($data, $receipt));
    }

    public function show(array $data)
    {
        $data['receipt'] = $this->receipt(decrypt($data['idencrypt']));
        $data['items'] = $data['receipt']->items;

        return view('tertag.trpnb.show', $data);
    }

    public function store(array $data)
    {
        DB::transaction(fn () => $this->save());

        return redirect('trpnb')->with(['message' => 'Penerimaan barang berhasil disimpan.', 'class' => 'success']);
    }

    public function update(array $data)
    {
        $receipt = $this->receipt(decrypt($data['idencrypt']));
        abort_unless($receipt->status === 'draf', 422, 'Hanya penerimaan berstatus draf yang dapat diubah.');
        DB::transaction(fn () => $this->save($receipt->id));

        return redirect('trpnb')->with(['message' => 'Penerimaan barang berhasil diperbarui.', 'class' => 'success']);
    }

    public function destroy(array $data)
    {
        $receipt = $this->receipt(decrypt($data['idencrypt']));
        abort_unless($receipt->status === 'draf', 422, 'Hanya penerimaan berstatus draf yang dapat dibatalkan.');
        $receipt->update(['status' => 'dibatalkan', 'user_update' => session('username')]);

        return redirect('trpnb')->with(['message' => 'Penerimaan barang dibatalkan.', 'class' => 'success']);
    }

    private function formData(array $data, ?PenerimaanBarang $receipt = null): array
    {
        $data['receipt'] = $receipt;
        $data['items'] = $receipt?->items ?? collect();
        $data['suppliers'] = DB::table('pemasok')->where('isactive', '1')->orderBy('nama')->get();
        $data['goods'] = DB::table('barang_jasa')->where('isactive', '1')->orderBy('nama_barang')->get(['kode_barang', 'nama_barang', 'satuan']);
        $data['orderLines'] = DB::table('rincian_pesanan_pembelian')->get(['id', 'barang_jasa_kode']);

        return $data;
    }

    private function receipt(int $id): PenerimaanBarang
    {
        return PenerimaanBarang::query()->join('pemasok as p', 'p.id', '=', 'penerimaan_barang.pemasok_id')
            ->select('penerimaan_barang.*', 'p.nama as pemasok')->findOrFail($id);
    }

    private function save(?int $id = null): void
    {
        $input = request()->validate([
            'pemasok_id' => ['required', 'exists:pemasok,id'],
            'nomor_form' => ['required', 'string', 'max:50', Rule::unique('penerimaan_barang', 'nomor_form')->ignore($id)],
            'tanggal_penerimaan' => ['required', 'date'],
            'nomor_penerimaan_pemasok' => ['nullable', 'string', 'max:50'],
            'keterangan' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.barang_jasa_kode' => ['required', 'exists:barang_jasa,kode_barang'],
            'items.*.kuantitas' => ['required', 'numeric', 'gt:0'],
            'items.*.rincian_pesanan_pembelian_id' => ['nullable', 'exists:rincian_pesanan_pembelian,id'],
        ]);
        $payload = collect($input)->except('items')->all() + ['user_update' => session('username')];
        $receipt = $id ? PenerimaanBarang::findOrFail($id) : PenerimaanBarang::create($payload + ['user_create' => session('username')]);
        if ($id) {
            $receipt->update($payload);
            $receipt->items()->delete();
        }
        $goods = DB::table('barang_jasa')->whereIn('kode_barang', collect($input['items'])->pluck('barang_jasa_kode'))->get()->keyBy('kode_barang');
        $receipt->items()->createMany(collect($input['items'])->map(function (array $item) use ($goods) {
            $goodsRow = $goods[$item['barang_jasa_kode']];

            return ['barang_jasa_kode' => $goodsRow->kode_barang, 'rincian_pesanan_pembelian_id' => $item['rincian_pesanan_pembelian_id'] ?: null, 'nama_barang_snapshot' => $goodsRow->nama_barang, 'satuan_snapshot' => $goodsRow->satuan, 'kuantitas' => $item['kuantitas']];
        })->all());
    }
}
