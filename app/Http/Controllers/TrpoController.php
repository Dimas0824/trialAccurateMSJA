<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class TrpoController extends Controller
{
    public function index(array $data)
    {
        // Ambil data ringkas untuk tabel daftar pesanan.
        $data['orders'] = DB::table('pesanan_pembelian as po')
            ->join('pemasok as p', 'p.id', '=', 'po.pemasok_id')
            ->leftJoin('mata_uang as m', 'm.id', '=', 'po.mata_uang_id')
            ->select('po.*', 'p.nama as pemasok', 'm.kode as mata_uang')
            ->latest('po.tanggal')->get();

        return view('rencan.trpo.list', $data);
    }

    public function add(array $data)
    {
        return view('rencan.trpo.add', $this->formData($data));
    }

    public function edit(array $data)
    {
        $order = $this->order(decrypt($data['idencrypt']));

        return view('rencan.trpo.edit', $this->formData($data, $order));
    }

    public function show(array $data)
    {
        // Detail memakai snapshot transaksi agar histori dokumen tidak berubah.
        $data['order'] = DB::table('pesanan_pembelian as po')
            ->join('pemasok as p', 'p.id', '=', 'po.pemasok_id')
            ->leftJoin('mata_uang as m', 'm.id', '=', 'po.mata_uang_id')
            ->leftJoin('syarat_pembayaran as sp', 'sp.id', '=', 'po.syarat_pembayaran_id')
            ->leftJoin('rekening_bank_pemasok as rb', 'rb.id', '=', 'po.rekening_bank_pemasok_id')
            ->select('po.*', 'p.nama as pemasok', 'm.kode as mata_uang', 'sp.nama as syarat_pembayaran', 'rb.nama_bank as bank_pemasok')
            ->where('po.id', decrypt($data['idencrypt']))->first() ?? abort(404);
        $data['items'] = DB::table('rincian_pesanan_pembelian as ri')
            ->leftJoin('pajak as p', 'p.id', '=', 'ri.pajak_id')
            ->select('ri.*', 'p.kode as kode_pajak', 'p.nama as nama_pajak')
            ->where('ri.pesanan_pembelian_id', $data['order']->id)->get();

        return view('rencan.trpo.show', $data);
    }

    public function store(array $data)
    {
        // todo: add data validation

        // Simpan header dan seluruh rincian sebagai satu transaksi.
        DB::transaction(fn () => $this->saveOrder());

        return redirect('trpo')->with(['message' => 'Pesanan pembelian berhasil disimpan.', 'class' => 'success']);
    }

    public function update(array $data)
    {
        $order = $this->order(decrypt($data['idencrypt']));
        // Dokumen selain draf harus tetap menjadi histori.
        abort_if($order->status !== 'draf', 422, 'Hanya pesanan berstatus draf yang dapat diubah.');

        DB::transaction(fn () => $this->saveOrder($order->id));

        return redirect('trpo')->with(['message' => 'Pesanan pembelian berhasil diperbarui.', 'class' => 'success']);
    }

    public function destroy(array $data)
    {
        $order = $this->order(decrypt($data['idencrypt']));
        // Pembatalan memakai status agar histori dokumen tidak hilang.
        abort_if(in_array($order->status, ['selesai', 'dibatalkan'], true), 422, 'Pesanan ini tidak dapat dibatalkan.');

        DB::table('pesanan_pembelian')->where('id', $order->id)->update([
            'status' => 'dibatalkan', 'user_update' => session('username'),
        ]);

        return redirect('trpo')->with(['message' => 'Pesanan pembelian dibatalkan.', 'class' => 'success']);
    }

    private function formData(array $data, ?object $order = null): array
    {
        // Sediakan pilihan master yang dipakai oleh form manual.
        $data['order'] = $order;
        $data['items'] = $order ? DB::table('rincian_pesanan_pembelian')->where('pesanan_pembelian_id', $order->id)->get() : collect();
        $data['suppliers'] = DB::table('pemasok')->where('isactive', '1')->orderBy('nama')->get();
        $data['currencies'] = DB::table('mata_uang')->where('isactive', '1')->orderBy('kode')->get();
        $data['terms'] = DB::table('syarat_pembayaran')->where('isactive', '1')->orderBy('nama')->get();
        $data['banks'] = DB::table('rekening_bank_pemasok')->orderBy('nama_bank')->get();
        $data['addresses'] = DB::table('alamat_perusahaan')->where('isactive', '1')->orderBy('nama')->get();
        $data['shippingMethods'] = DB::table('metode_pengiriman')->where('isactive', '1')->orderBy('nama')->get();
        $data['fobs'] = DB::table('ketentuan_fob')->where('isactive', '1')->orderBy('nama')->get();
        // Master barang memakai nama_barang setelah migrasi rebuild.
        $data['goods'] = DB::table('barang_jasa')->where('isactive', '1')->orderBy('nama_barang')->get();
        $data['taxes'] = DB::table('pajak')->where('isactive', '1')->orderBy('nama')->get()->keyBy('id');

        return $data;
    }

    private function order(int $id): object
    {
        return DB::table('pesanan_pembelian')->find($id) ?? abort(404);
    }

    private function saveOrder(?int $id = null): void
    {
        // Validasi header dan minimal satu baris barang sebelum menghitung total.
        $input = request()->validate([
            'pemasok_id' => ['required', 'exists:pemasok,id'],
            'nomor' => ['required', 'string', 'max:50', Rule::unique('pesanan_pembelian', 'nomor')->ignore($id)],
            'tanggal' => ['required', 'date'],
            'tanggal_pengiriman' => ['nullable', 'date', 'after_or_equal:tanggal'],
            'mata_uang_id' => ['nullable', 'exists:mata_uang,id'],
            'syarat_pembayaran_id' => ['nullable', 'exists:syarat_pembayaran,id'],
            'rekening_bank_pemasok_id' => ['nullable', 'exists:rekening_bank_pemasok,id'],
            'alamat_pengiriman_id' => ['nullable', 'exists:alamat_perusahaan,id'],
            'metode_pengiriman_id' => ['nullable', 'exists:metode_pengiriman,id'],
            'ketentuan_fob_id' => ['nullable', 'exists:ketentuan_fob,id'],
            'keterangan' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.barang_jasa_kode' => ['required', 'distinct', 'exists:barang_jasa,kode_barang'],
            'items.*.kuantitas' => ['required', 'numeric', 'gt:0'],
            'items.*.harga_satuan' => ['required', 'numeric', 'min:0'],
            'items.*.diskon_persen' => ['nullable', 'numeric', 'between:0,100'],
            'items.*.pajak_id' => ['nullable', 'exists:pajak,id'],
            'items.*.keterangan' => ['nullable', 'string'],
        ]);
        $supplier = DB::table('pemasok')->find($input['pemasok_id']);
        $totals = ['subtotal' => 0.0, 'jumlah_pajak' => 0.0];
        $lines = [];

        foreach ($input['items'] as $line) {
            // Ambil master terbaru hanya untuk membuat snapshot pada dokumen.
            $goods = DB::table('barang_jasa')->where('kode_barang', $line['barang_jasa_kode'])->first();
            $quantity = (float) $line['kuantitas'];
            $price = (float) $line['harga_satuan'];
            $discountRate = (float) ($line['diskon_persen'] ?? 0);
            $discount = round($quantity * $price * $discountRate / 100, 2);
            $lineTotal = round($quantity * $price - $discount, 2);
            $taxRate = request()->boolean('kena_pajak') && ! empty($line['pajak_id'])
                ? (float) DB::table('pajak')->where('id', $line['pajak_id'])->value('tarif') : 0.0;
            $tax = request()->boolean('total_termasuk_pajak')
                ? round($lineTotal * $taxRate / (100 + $taxRate), 2)
                : round($lineTotal * $taxRate / 100, 2);
            // Pisahkan nilai barang dan pajak agar total dokumen konsisten.
            $totals['subtotal'] += request()->boolean('total_termasuk_pajak') ? $lineTotal - $tax : $lineTotal;
            $totals['jumlah_pajak'] += $tax;
            $lines[] = [
                'barang_jasa_kode' => $goods->kode_barang,
                'pajak_id' => $line['pajak_id'] ?? null, 'kode_barang_snapshot' => $goods->kode_barang,
                'nama_barang_snapshot' => $goods->nama_barang, 'nama_satuan_snapshot' => $goods->satuan,
                'kuantitas' => $quantity, 'harga_satuan' => $price, 'diskon_persen' => $discountRate,
                'jumlah_diskon' => $discount, 'total_baris' => $lineTotal,
                'keterangan' => $line['keterangan'] ?? null,
            ];
        }
        $totals = array_map(fn ($value) => round($value, 2), $totals);
        $payload = request()->only(['pemasok_id', 'mata_uang_id', 'urutan_penomoran_id', 'syarat_pembayaran_id', 'rekening_bank_pemasok_id', 'alamat_pengiriman_id', 'metode_pengiriman_id', 'ketentuan_fob_id', 'nomor', 'tanggal', 'tanggal_pengiriman', 'keterangan']);
        $payload += [
            'kode_pemasok_snapshot' => $supplier->kode_pemasok, 'nama_pemasok_snapshot' => $supplier->nama,
            'kena_pajak' => request()->boolean('kena_pajak'), 'total_termasuk_pajak' => request()->boolean('total_termasuk_pajak'),
            'subtotal' => $totals['subtotal'], 'jumlah_pajak' => $totals['jumlah_pajak'],
            'total_akhir' => $totals['subtotal'] + $totals['jumlah_pajak'], 'user_update' => session('username'),
        ];
        // Simpan header dahulu agar rincian memiliki ID pesanan yang benar.
        $orderId = $id ?? DB::table('pesanan_pembelian')->insertGetId($payload + ['user_create' => session('username')]);
        if ($id) {
            // Rincian draf diganti utuh karena belum dipakai dokumen lanjutan.
            DB::table('pesanan_pembelian')->where('id', $id)->update($payload);
            DB::table('rincian_pesanan_pembelian')->where('pesanan_pembelian_id', $id)->delete();
        }
        DB::table('rincian_pesanan_pembelian')->insert(array_map(fn ($line) => $line + ['pesanan_pembelian_id' => $orderId], $lines));
    }
}
