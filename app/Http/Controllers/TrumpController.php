<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class TrumpController extends Controller
{
    public function index(array $data)
    {
        // Ambil uang muka beserta nama pemasok dan nomor pesanan bila ada.
        $data['advances'] = DB::table('uang_muka_pembelian as um')
            ->join('pemasok as p', 'p.id', '=', 'um.pemasok_id')
            ->leftJoin('pesanan_pembelian as po', 'po.id', '=', 'um.pesanan_pembelian_id')
            ->select('um.*', 'p.nama as pemasok', 'po.nomor as nomor_pesanan')
            ->latest('um.tanggal')->get();

        return view('rencan.trump.list', $data);
    }

    public function add(array $data)
    {
        return view('rencan.trump.add', $this->formData($data));
    }

    public function edit(array $data)
    {
        $advance = $this->advance(decrypt($data['idencrypt']));

        return view('rencan.trump.edit', $this->formData($data, $advance));
    }

    public function show(array $data)
    {
        // Ambil dokumen dan referensinya untuk tampilan histori uang muka.
        $data['advance'] = DB::table('uang_muka_pembelian as um')
            ->join('pemasok as p', 'p.id', '=', 'um.pemasok_id')
            ->leftJoin('pesanan_pembelian as po', 'po.id', '=', 'um.pesanan_pembelian_id')
            ->leftJoin('mata_uang as m', 'm.id', '=', 'um.mata_uang_id')
            ->leftJoin('syarat_pembayaran as sp', 'sp.id', '=', 'um.syarat_pembayaran_id')
            ->leftJoin('rekening_bank_pemasok as rb', 'rb.id', '=', 'um.rekening_bank_pemasok_id')
            ->select('um.*', 'p.nama as pemasok', 'po.nomor as nomor_pesanan', 'm.kode as mata_uang', 'sp.nama as syarat_pembayaran', 'rb.nama_bank as bank_pemasok')
            ->where('um.id', decrypt($data['idencrypt']))->first() ?? abort(404);

        return view('rencan.trump.show', $data);
    }

    public function store(array $data)
    {
        // Simpan uang muka secara atomik.
        DB::transaction(fn () => $this->saveAdvance());

        return redirect('trump')->with(['message' => 'Uang muka pembelian berhasil disimpan.', 'class' => 'success']);
    }

    public function update(array $data)
    {
        $advance = $this->advance(decrypt($data['idencrypt']));
        // Uang muka yang sudah diproses tidak boleh diubah dari menu ini.
        abort_if($advance->status !== 'draf', 422, 'Hanya uang muka berstatus draf yang dapat diubah.');
        DB::transaction(fn () => $this->saveAdvance($advance->id));

        return redirect('trump')->with(['message' => 'Uang muka pembelian berhasil diperbarui.', 'class' => 'success']);
    }

    public function destroy(array $data)
    {
        $advance = $this->advance(decrypt($data['idencrypt']));
        // Pembatalan menjaga histori pembayaran tetap tersedia.
        abort_if($advance->status !== 'draf', 422, 'Hanya uang muka berstatus draf yang dapat dibatalkan.');
        DB::table('uang_muka_pembelian')->where('id', $advance->id)->update(['status' => 'dibatalkan', 'user_update' => session('username')]);

        return redirect('trump')->with(['message' => 'Uang muka pembelian dibatalkan.', 'class' => 'success']);
    }

    private function formData(array $data, ?object $advance = null): array
    {
        // Sediakan pilihan master yang dipakai oleh form uang muka.
        $data['advance'] = $advance;
        $data['suppliers'] = DB::table('pemasok')->where('isactive', '1')->orderBy('nama')->get();
        $data['orders'] = DB::table('pesanan_pembelian')->whereNotIn('status', ['dibatalkan'])->orderByDesc('tanggal')->get();
        $data['currencies'] = DB::table('mata_uang')->where('isactive', '1')->orderBy('kode')->get();
        $data['terms'] = DB::table('syarat_pembayaran')->where('isactive', '1')->orderBy('nama')->get();
        $data['banks'] = DB::table('rekening_bank_pemasok')->orderBy('nama_bank')->get();

        return $data;
    }

    private function advance(int $id): object
    {
        return DB::table('uang_muka_pembelian')->find($id) ?? abort(404);
    }

    private function saveAdvance(?int $id = null): void
    {
        // Validasi nilai dan referensi sebelum menyimpan uang muka.
        $input = request()->validate([
            'pemasok_id' => ['required', 'exists:pemasok,id'],
            'pesanan_pembelian_id' => ['nullable', 'exists:pesanan_pembelian,id'],
            'nomor_form' => ['required', 'string', 'max:50', Rule::unique('uang_muka_pembelian', 'nomor_form')->ignore($id)],
            'tanggal' => ['required', 'date'], 'nomor_faktur_pemasok' => ['required', 'string', 'max:50'],
            'mata_uang_id' => ['nullable', 'exists:mata_uang,id'], 'syarat_pembayaran_id' => ['nullable', 'exists:syarat_pembayaran,id'],
            'rekening_bank_pemasok_id' => ['nullable', 'exists:rekening_bank_pemasok,id'],
            'alamat' => ['nullable', 'string'], 'keterangan' => ['nullable', 'string'],
            'jumlah_uang_muka' => ['required', 'numeric', 'gt:0'], 'jumlah_pajak' => ['nullable', 'numeric', 'min:0'],
        ]);
        if (! empty($input['pesanan_pembelian_id'])) {
            // Cegah uang muka dikaitkan ke pesanan milik pemasok lain.
            abort_unless(DB::table('pesanan_pembelian')->where(['id' => $input['pesanan_pembelian_id'], 'pemasok_id' => $input['pemasok_id']])->exists(), 422, 'Pesanan pembelian harus milik pemasok yang dipilih.');
        }
        $supplier = DB::table('pemasok')->find($input['pemasok_id']);
        $amount = round((float) $input['jumlah_uang_muka'], 2);
        $tax = request()->boolean('kena_pajak') ? round((float) ($input['jumlah_pajak'] ?? 0), 2) : 0;
        $included = request()->boolean('total_termasuk_pajak');
        $payload = request()->only(['pemasok_id', 'pesanan_pembelian_id', 'mata_uang_id', 'urutan_penomoran_id', 'rekening_bank_pemasok_id', 'syarat_pembayaran_id', 'nomor_form', 'tanggal', 'nomor_faktur_pemasok', 'alamat', 'keterangan']);
        $payload += [
            'kode_pemasok_snapshot' => $supplier->kode_pemasok, 'nama_pemasok_snapshot' => $supplier->nama,
            'jumlah_uang_muka' => $amount, 'kena_pajak' => request()->boolean('kena_pajak'), 'total_termasuk_pajak' => $included,
            'subtotal' => $included ? $amount - $tax : $amount, 'jumlah_pajak' => $tax,
            'total_akhir' => $included ? $amount : $amount + $tax, 'user_update' => session('username'),
        ];
        if ($id) {
            // Hanya dokumen draf yang sampai ke cabang pembaruan ini.
            DB::table('uang_muka_pembelian')->where('id', $id)->update($payload);
        } else {
            DB::table('uang_muka_pembelian')->insert($payload + ['user_create' => session('username')]);
        }
    }
}
