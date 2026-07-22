<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class MspemsController extends Controller
{
    public function index($data)
    {
        $data['suppliers'] = DB::table('pemasok as p')
            ->leftJoin('kategori_pemasok as k', 'k.id', '=', 'p.kategori_pemasok_id')
            ->leftJoin('saldo_awal_utang_pemasok as s', 's.pemasok_id', '=', 'p.id')
            ->select('p.*', 'k.nama as kategori', DB::raw('coalesce(sum(s.jumlah), 0) as saldo_utang'))
            ->groupBy('p.id')
            ->orderBy('p.nama')
            ->get();

        return view('master.mspems.list', $data);
    }

    public function add($data)
    {
        return view('master.mspems.add', $this->formData($data));
    }

    public function edit($data)
    {
        $supplier = $this->supplier(decrypt($data['idencrypt']));

        return view('master.mspems.edit', $this->formData($data, $supplier));
    }

    public function store($data)
    {
        DB::transaction(fn () => $this->saveSupplier());

        return redirect('mspems')->with(['message' => 'Pemasok berhasil ditambahkan.', 'class' => 'success']);
    }

    public function update($data)
    {
        DB::transaction(fn () => $this->saveSupplier(decrypt($data['idencrypt'])));

        return redirect('mspems')->with(['message' => 'Pemasok berhasil diperbarui.', 'class' => 'success']);
    }

    public function destroy($data)
    {
        $supplier = DB::table('pemasok')->find(decrypt($data['idencrypt']));
        abort_unless($supplier, 404);

        DB::table('pemasok')->where('id', $supplier->id)->update([
            'isactive' => $supplier->isactive === '1' ? '0' : '1',
            'user_update' => session('username'),
        ]);

        return redirect('mspems')->with(['message' => 'Status pemasok diperbarui.', 'class' => 'success']);
    }

    private function formData(array $data, ?object $supplier = null): array
    {
        $data['supplier'] = $supplier;
        $data['categories'] = DB::table('kategori_pemasok')->where('isactive', '1')->orderBy('nama')->get();
        $data['numberFormats'] = DB::table('urutan_penomoran')->orderBy('nama')->get();
        $data['paymentTerms'] = DB::table('syarat_pembayaran')->where('isactive', '1')->orderBy('nama')->get();
        $data['accounts'] = DB::table('akun_perkiraan')->where('isactive', '1')->orderBy('kode_akun')->get();
        $data['currencies'] = DB::table('mata_uang')->where('isactive', '1')->orderBy('kode')->get();
        $data['contacts'] = $supplier ? DB::table('kontak_pemasok')->where('pemasok_id', $supplier->id)->get() : collect();
        $data['bankAccounts'] = $supplier ? DB::table('rekening_bank_pemasok')->where('pemasok_id', $supplier->id)->get() : collect();
        $data['openingBalances'] = $supplier ? DB::table('saldo_awal_utang_pemasok')->where('pemasok_id', $supplier->id)->get() : collect();
        $data['paymentAddress'] = $supplier ? DB::table('alamat_pemasok')->where(['pemasok_id' => $supplier->id, 'tipe_alamat' => 'pembayaran'])->first() : null;
        $data['taxAddress'] = $supplier ? DB::table('alamat_pemasok')->where(['pemasok_id' => $supplier->id, 'tipe_alamat' => 'pajak'])->first() : null;
        $data['taxProfile'] = $supplier ? DB::table('profil_pajak_pemasok')->where('pemasok_id', $supplier->id)->first() : null;

        return $data;
    }

    private function supplier(int $id): object
    {
        return DB::table('pemasok')->find($id) ?? abort(404);
    }

    private function saveSupplier(?int $id = null): void
    {
        $rules = [
            'nama' => ['required', 'string', 'max:150'],
            'kode_pemasok' => ['required', 'string', 'max:50', Rule::unique('pemasok', 'kode_pemasok')->ignore($id)],
            'email' => ['nullable', 'email', 'max:150'],
            'kategori_pemasok_id' => ['nullable', 'exists:kategori_pemasok,id'],
            'tipe_pemasok' => ['nullable', Rule::in(['perorangan', 'perusahaan', 'pemerintah'])],
            'contacts.*.nama_lengkap' => ['nullable', 'string', 'max:150'],
            'bank_accounts.*.nomor_rekening' => ['nullable', 'string', 'max:50'],
            'opening_balances.*.tanggal' => ['nullable', 'date'],
            'opening_balances.*.jumlah' => ['nullable', 'numeric'],
        ];
        $input = request()->validate($rules);
        $payload = request()->only(['kategori_pemasok_id', 'urutan_penomoran_id', 'kode_pemasok', 'nama', 'telepon_bisnis', 'nomor_handphone', 'nomor_whatsapp', 'email', 'nomor_faksimili', 'situs_web', 'tipe_pemasok', 'syarat_pembayaran_id', 'diskon_default_persen', 'deskripsi_default', 'akun_pembelian_id', 'akun_utang_id', 'akun_uang_muka_id', 'catatan']);
        $payload += [
            'penomoran_otomatis' => request()->boolean('penomoran_otomatis'),
            'penjual_jasa_orang_pribadi' => request()->boolean('penjual_jasa_orang_pribadi'),
            'memberikan_nomor_faktur' => request()->boolean('memberikan_nomor_faktur'),
            'user_update' => session('username'),
        ];
        $supplierId = $id ?? DB::table('pemasok')->insertGetId($payload + ['user_create' => session('username')]);
        if ($id) {
            DB::table('pemasok')->where('id', $id)->update($payload);
        }

        foreach (['pembayaran' => 'payment_address', 'pajak' => 'tax_address'] as $type => $key) {
            $address = request($key, []);
            DB::table('alamat_pemasok')->updateOrInsert(['pemasok_id' => $supplierId, 'tipe_alamat' => $type], ['jalan' => $address['jalan'] ?? null, 'kota' => $address['kota'] ?? null, 'kode_pos' => $address['kode_pos'] ?? null, 'provinsi' => $address['provinsi'] ?? null, 'negara' => $address['negara'] ?? null]);
        }

        DB::table('profil_pajak_pemasok')->updateOrInsert(['pemasok_id' => $supplierId], request()->only(['tipe_identitas_pajak', 'nomor_wajib_pajak', 'nama_wajib_pajak', 'nitku', 'tipe_transaksi']) + ['faktur_default_termasuk_pajak' => request()->boolean('faktur_default_termasuk_pajak'), 'alamat_pajak_sama_dengan_pembayaran' => request()->boolean('alamat_pajak_sama_dengan_pembayaran')]);

        // ponytail: child rows are recreated while no transaction table references them; replace with keyed upserts when external references exist.
        foreach (['kontak_pemasok' => 'contacts', 'rekening_bank_pemasok' => 'bank_accounts', 'saldo_awal_utang_pemasok' => 'opening_balances'] as $table => $key) {
            DB::table($table)->where('pemasok_id', $supplierId)->delete();
            foreach (request($key, []) as $row) {
                if (array_filter($row, fn ($value) => $value !== null && $value !== '')) {
                    DB::table($table)->insert($row + ['pemasok_id' => $supplierId]);
                }
            }
        }
    }
}
