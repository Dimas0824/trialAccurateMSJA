<?php

namespace App\Http\Controllers;

use App\Models\ReturPembelian;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

// controller untuk retur pembelian
// yang mengelola daftar, tambah, ubah, hapus, dan detail retur pembelian.
class TrretpController extends Controller
{
    public function index(array $data)
    {
        $data['returns'] = ReturPembelian::query()->join('pemasok', 'pemasok.id', '=', 'retur_pembelian.pemasok_id')
            ->select('retur_pembelian.*', 'pemasok.nama as pemasok')->latest('tanggal_retur')->get();

        return view('koreks.trretp.list', $data);
    }

    public function add(array $data)
    {
        return view('koreks.trretp.add', $this->formData($data));
    }

    public function edit(array $data)
    {
        $return = ReturPembelian::findOrFail(decrypt($data['idencrypt']));
        abort_unless($return->status === 'draf', 422, 'Hanya retur berstatus draf yang dapat diubah.');

        return view('koreks.trretp.edit', $this->formData($data, $return));
    }

    public function show(array $data)
    {
        $data['return'] = ReturPembelian::findOrFail(decrypt($data['idencrypt']));
        $data['items'] = $data['return']->items;

        return view('koreks.trretp.show', $data);
    }

    public function store(array $data)
    {
        DB::transaction(fn () => $this->save());

        return redirect('trretp')->with(['message' => 'Retur pembelian berhasil disimpan.', 'class' => 'success']);
    }

    public function update(array $data)
    {
        $return = ReturPembelian::findOrFail(decrypt($data['idencrypt']));
        abort_unless($return->status === 'draf', 422, 'Hanya retur berstatus draf yang dapat diubah.');
        DB::transaction(fn () => $this->save($return));

        return redirect('trretp')->with(['message' => 'Retur pembelian berhasil diperbarui.', 'class' => 'success']);
    }

    public function destroy(array $data)
    {
        $return = ReturPembelian::findOrFail(decrypt($data['idencrypt']));
        abort_unless($return->status === 'draf', 422, 'Retur yang diposting tidak dapat dibatalkan.');
        $return->update(['status' => 'dibatalkan', 'user_update' => session('username')]);

        return redirect('trretp')->with(['message' => 'Retur pembelian dibatalkan.', 'class' => 'success']);
    }

    private function formData(array $data, ?ReturPembelian $return = null): array
    {
        $data['return'] = $return;
        $data['items'] = $return?->items ?? collect();
        $data['suppliers'] = DB::table('pemasok')->where('isactive', '1')->orderBy('nama')->get();
        $data['goods'] = DB::table('barang_jasa')->where('isactive', '1')->orderBy('nama_barang')->get(['kode_barang', 'nama_barang', 'satuan']);
        $data['invoices'] = DB::table('faktur_pembelian')->where('status', '!=', 'dibatalkan')->get(['id', 'pemasok_id', 'nomor_form']);
        $data['receipts'] = DB::table('penerimaan_barang')->where('status', '!=', 'dibatalkan')->get(['id', 'pemasok_id', 'nomor_form']);

        return $data;
    }

    private function save(?ReturPembelian $return = null): void
    {
        $input = request()->validate([
            'pemasok_id' => ['required', 'exists:pemasok,id'], 'nomor_retur' => ['required', 'string', 'max:50', Rule::unique('retur_pembelian', 'nomor_retur')->ignore($return?->id)],
            'tanggal_retur' => ['required', 'date'], 'sumber_retur' => ['required', Rule::in(['faktur', 'penerimaan_barang'])],
            'faktur_pembelian_id' => ['nullable', 'required_if:sumber_retur,faktur', 'exists:faktur_pembelian,id'],
            'penerimaan_barang_id' => ['nullable', 'required_if:sumber_retur,penerimaan_barang', 'exists:penerimaan_barang,id'], 'keterangan' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'], 'items.*.barang_jasa_kode' => ['required', 'distinct', 'exists:barang_jasa,kode_barang'],
            'items.*.kuantitas' => ['required', 'numeric', 'gt:0'], 'items.*.harga_satuan' => ['nullable', 'numeric', 'min:0'],
        ]);
        $source = $input['sumber_retur'] === 'faktur'
            ? DB::table('faktur_pembelian')->find($input['faktur_pembelian_id'])
            : DB::table('penerimaan_barang')->find($input['penerimaan_barang_id']);
        abort_unless($source && (int) $source->pemasok_id === (int) $input['pemasok_id'], 422, 'Dokumen sumber harus milik pemasok yang dipilih.');
        $goods = DB::table('barang_jasa')->whereIn('kode_barang', collect($input['items'])->pluck('barang_jasa_kode'))->get()->keyBy('kode_barang');
        $lines = collect($input['items'])->map(function (array $item) use ($goods) {
            $good = $goods[$item['barang_jasa_kode']];
            $quantity = (float) $item['kuantitas'];
            $price = (float) ($item['harga_satuan'] ?? 0);

            return ['barang_jasa_kode' => $good->kode_barang, 'nama_barang_snapshot' => $good->nama_barang, 'satuan_snapshot' => $good->satuan, 'kuantitas' => $quantity, 'harga_satuan' => $price, 'total_baris' => round($quantity * $price, 2)];
        });
        $total = round($lines->sum('total_baris'), 2);
        $payload = collect($input)->except('items')->all() + ['faktur_pembelian_id' => $input['sumber_retur'] === 'faktur' ? $input['faktur_pembelian_id'] : null, 'penerimaan_barang_id' => $input['sumber_retur'] === 'penerimaan_barang' ? $input['penerimaan_barang_id'] : null, 'subtotal' => $total, 'total_akhir' => $total, 'user_update' => session('username')];
        $return ??= ReturPembelian::create($payload + ['user_create' => session('username')]);
        if ($return->wasRecentlyCreated === false) {
            $return->update($payload);
            $return->items()->delete();
        }
        $return->items()->createMany($lines->all());
    }
}
