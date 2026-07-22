<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class menu_master_barang_jasa extends Seeder
{
    public function run(): void
    {
        // ─── 1. Hapus data lama (jika pernah dijalankan) ─────────────────────
        DB::table('sys_auth')->where('dmenu', 'msbrgj')->delete();
        DB::table('sys_table')->where('dmenu', 'msbrgj')->delete();
        DB::table('sys_id')->where('dmenu', 'msbrgj')->delete();
        DB::table('sys_dmenu')->where('dmenu', 'msbrgj')->delete();

        // Hapus sys_enum untuk master barang jasa
        DB::table('sys_enum')->where('idenum', 'kategori_barang')->delete();
        DB::table('sys_enum')->where('idenum', 'jenis_barang')->delete();
        DB::table('sys_enum')->where('idenum', 'satuan_barang')->delete();

        // ─── 2. Pastikan gmenu 'master' sudah ada ────────────────────────────
        DB::table('sys_gmenu')->updateOrInsert(
            ['gmenu' => 'master'],
            ['urut' => 2, 'name' => 'Master', 'icon' => 'ni-collection', 'isactive' => '1']
        );

        // ─── 3. Daftarkan menu di sys_dmenu ─────────────────────────────────
        DB::table('sys_dmenu')->insert([
            'gmenu'    => 'master',
            'dmenu'    => 'msbrgj',
            'urut'     => 4,                     // setelah Pemasok (urut=3)
            'name'     => 'Barang & Jasa',
            'url'      => 'msbrgj',
            'icon'     => 'ni-box-2',
            'tabel'    => 'barang_jasa',
            'layout'   => 'standr',              // single-PK CRUD
            'show'     => '1',
            'isactive' => '1',
            'js'       => '0',
            'notif'    => "select count(*) as 'notif' from barang_jasa where isactive = '1'",
        ]);

        // ─── 4. Konfigurasi kolom di sys_table ──────────────────────────────
        $columns = [
            // ── Kolom kiri (position='3') ───────────────────────────────────
            [
                'urut'       => '1',
                'field'      => 'kode_barang',
                'alias'      => 'Kode Barang',
                'type'       => 'char',
                'length'     => '50',
                'validate'   => 'required|max:50|min:1|unique:barang_jasa,kode_barang',
                'primary'    => '1',
                'filter'     => '1',
                'list'       => '1',
                'show'       => '0',              // auto-generated, hidden di form
                'query'      => '',
                'class'      => 'readonly',
                'generateid' => 'kode_barang',    // trigger auto-numbering
                'position'   => '3',
                'note'       => 'Otomatis dari sistem (BRG00001, ...)',
            ],
            [
                'urut'       => '2',
                'field'      => 'nama_barang',
                'alias'      => 'Nama Barang',
                'type'       => 'string',
                'length'     => '150',
                'validate'   => 'required|max:150|min:2',
                'primary'    => '0',
                'filter'     => '1',
                'list'       => '1',
                'show'       => '1',
                'query'      => '',
                'class'      => '',
                'generateid' => '',
                'position'   => '3',
            ],
            [
                'urut'       => '3',
                'field'      => 'kategori_barang',
                'alias'      => 'Kategori',
                'type'       => 'enum',
                'length'     => '10',
                'validate'   => 'required',
                'primary'    => '0',
                'filter'     => '1',
                'list'       => '1',
                'show'       => '1',
                'query'      => "select value, name from sys_enum where idenum = 'kategori_barang' and isactive = '1'",
                'class'      => '',
                'generateid' => '',
                'position'   => '3',
            ],
            [
                'urut'       => '4',
                'field'      => 'jenis_barang',
                'alias'      => 'Jenis Barang',
                'type'       => 'enum',
                'length'     => '20',
                'validate'   => 'required',
                'primary'    => '0',
                'filter'     => '1',
                'list'       => '1',
                'show'       => '1',
                'query'      => "select value, name from sys_enum where idenum = 'jenis_barang' and isactive = '1'",
                'class'      => '',
                'generateid' => '',
                'position'   => '3',
            ],
            // ── Kolom kanan (position='4') ──────────────────────────────────
            [
                'urut'       => '5',
                'field'      => 'upc_barcode',
                'alias'      => 'UPC / Barcode',
                'type'       => 'string',
                'length'     => '50',
                'validate'   => 'nullable|max:50',
                'primary'    => '0',
                'filter'     => '1',
                'list'       => '1',
                'show'       => '1',
                'query'      => '',
                'class'      => '',
                'generateid' => '',
                'position'   => '4',
            ],
            [
                'urut'       => '6',
                'field'      => 'satuan',
                'alias'      => 'Satuan',
                'type'       => 'enum',
                'length'     => '10',
                'validate'   => 'required',
                'primary'    => '0',
                'filter'     => '1',
                'list'       => '1',
                'show'       => '1',
                'query'      => "select value, name from sys_enum where idenum = 'satuan_barang' and isactive = '1'",
                'class'      => '',
                'generateid' => '',
                'position'   => '4',
            ],
            [
                'urut'       => '7',
                'field'      => 'merk_barang',
                'alias'      => 'Merk Barang',
                'type'       => 'string',
                'length'     => '100',
                'validate'   => 'nullable|max:100',
                'primary'    => '0',
                'filter'     => '1',
                'list'       => '1',
                'show'       => '1',
                'query'      => '',
                'class'      => '',
                'generateid' => '',
                'position'   => '4',
            ],
            // ── Status (selalu di kanan, show=0 = hidden di form) ────────────
            [
                'urut'       => '8',
                'field'      => 'isactive',
                'alias'      => 'Status',
                'type'       => 'enum',
                'length'     => '1',
                'validate'   => '',
                'primary'    => '0',
                'filter'     => '1',
                'list'       => '1',
                'show'       => '0',
                'query'      => "select value, name from sys_enum where idenum = 'isactive' and isactive = '1'",
                'class'      => '',
                'generateid' => '',
                'position'   => '4',
            ],
        ];

        foreach ($columns as $col) {
            DB::table('sys_table')->insert([
                'gmenu'      => 'master',
                'dmenu'      => 'msbrgj',
                'default'    => '',
                'decimals'   => '0',
                'sub'        => '',
                'position'   => $col['position'],
                'note'       => $col['note'] ?? '',
            ] + $col);
        }

        // ─── 5. Hak akses ────────────────────────────────────────────────────
        foreach ([
            [
                'idroles' => 'admins',
                'add'     => '1', 'edit' => '1', 'delete' => '1',
                'print'   => '1', 'excel' => '1', 'pdf' => '1',
                'rules'   => '0',
            ],
            [
                'idroles' => 'pembel',
                'add'     => '1', 'edit' => '1', 'delete' => '1',
                'print'   => '0', 'excel' => '0', 'pdf' => '0',
                'rules'   => '0',
            ],
        ] as $auth) {
            DB::table('sys_auth')->insert($auth + [
                'gmenu'    => 'master',
                'dmenu'    => 'msbrgj',
                'approval' => '0',
                'value'    => '0',
                'isactive' => '1',
            ]);
        }

        // ─── 6. Nilai enum untuk select option ──────────────────────────────
        $enums = [
            // kategori_barang
            ['idenum' => 'kategori_barang', 'value' => 'umum', 'name' => 'Umum'],
            ['idenum' => 'kategori_barang', 'value' => 'jasa', 'name' => 'Jasa'],
            // jenis_barang
            ['idenum' => 'jenis_barang',   'value' => 'prsd',     'name' => 'Persediaan'],
            ['idenum' => 'jenis_barang',   'value' => 'non_prsd', 'name' => 'Non Persediaan'],
            ['idenum' => 'jenis_barang',   'value' => 'jasa',           'name' => 'Jasa'],
            ['idenum' => 'jenis_barang',   'value' => 'grup',           'name' => 'Grup'],
            // satuan
            ['idenum' => 'satuan_barang',  'value' => 'Jam',  'name' => 'Jam'],
            ['idenum' => 'satuan_barang',  'value' => 'Liter', 'name' => 'Liter'],
            ['idenum' => 'satuan_barang',  'value' => 'KG',   'name' => 'KG'],
            ['idenum' => 'satuan_barang',  'value' => 'PCS',  'name' => 'PCS'],
        ];

        foreach ($enums as $e) {
            DB::table('sys_enum')->insert($e + ['isactive' => '1']);
        }

        // ─── 7. Konfigurasi penomoran otomatis (sys_id) ─────────────────────
        // Format: BRG + 5-digit counter → BRG00001, BRG00002, ...
        DB::table('sys_id')->insert([
            'dmenu'    => 'msbrgj',
            'source'   => 'ext',          // external: string tetap
            'internal' => '',
            'external' => 'BRG',          // prefix
            'urut'     => 1,
            'length'   => 3,              // ambil 3 karakter pertama dari 'BRG'
            'isactive' => '1',
        ]);
        DB::table('sys_id')->insert([
            'dmenu'    => 'msbrgj',
            'source'   => 'cnt',          // counter otomatis
            'internal' => '',
            'external' => '',
            'urut'     => 2,
            'length'   => 5,              // 5 digit → 00001, 00002, ...
            'isactive' => '1',
        ]);
    }
}
