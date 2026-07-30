<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

// controller untuk transaksi penerimaan barang (TRPNB)
// yang mengelola daftar, tambah, ubah, hapus, dan detail penerimaan barang.
class TrpnbController extends Controller
{
    public function index(array $data)
    {
        $data['receipts'] = DB::table('penerimaan_barang as pb')
            ->join('pemasok as p', 'p.id', '=', 'pb.pemasok_id')
            ->select('pb.*', 'p.nama as pemasok')
            ->latest('pb.tanggal_penerimaan')->get();

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
        $data['items'] = DB::table('rincian_penerimaan_barang')->where('penerimaan_barang_id', $data['receipt']->id)->get();

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
        DB::table('penerimaan_barang')->where('id', $receipt->id)->update(['status' => 'dibatalkan', 'user_update' => session('username')]);

        return redirect('trpnb')->with(['message' => 'Penerimaan barang dibatalkan.', 'class' => 'success']);
    }

    private function formData(array $data, ?object $receipt = null): array
    {
        $data['receipt'] = $receipt;
        $data['items'] = $receipt ? DB::table('rincian_penerimaan_barang')->where('penerimaan_barang_id', $receipt->id)->get() : [];
        $data['suppliers'] = DB::table('pemasok')->where('isactive', '1')->orderBy('nama')->get();
        $data['goods'] = DB::table('barang_jasa')->where('isactive', '1')->orderBy('nama_barang')->get(['kode_barang', 'nama_barang', 'satuan']);
        $data['orderLines'] = DB::table('rincian_pesanan_pembelian')->get(['id', 'barang_jasa_kode']);

        return $data;
    }

    private function receipt(int $id): object
    {
        return DB::table('penerimaan_barang as pb')->join('pemasok as p', 'p.id', '=', 'pb.pemasok_id')
            ->select('pb.*', 'p.nama as pemasok')->where('pb.id', $id)->first() ?? abort(404);
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
        $receiptId = $id ?? DB::table('penerimaan_barang')->insertGetId($payload + ['user_create' => session('username')]);
        if ($id) {
            DB::table('penerimaan_barang')->where('id', $id)->update($payload);
            DB::table('rincian_penerimaan_barang')->where('penerimaan_barang_id', $id)->delete();
        }
        $goods = DB::table('barang_jasa')->whereIn('kode_barang', collect($input['items'])->pluck('barang_jasa_kode'))->get()->keyBy('kode_barang');
        DB::table('rincian_penerimaan_barang')->insert(collect($input['items'])->map(function (array $item) use ($goods, $receiptId) {
            $goodsRow = $goods[$item['barang_jasa_kode']];

            return ['penerimaan_barang_id' => $receiptId, 'barang_jasa_kode' => $goodsRow->kode_barang, 'rincian_pesanan_pembelian_id' => $item['rincian_pesanan_pembelian_id'] ?: null, 'nama_barang_snapshot' => $goodsRow->nama_barang, 'satuan_snapshot' => $goodsRow->satuan, 'kuantitas' => $item['kuantitas']];
        })->all());
    }
}
