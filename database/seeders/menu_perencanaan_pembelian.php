<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class menu_perencanaan_pembelian extends Seeder
{
    public function run(): void
    {
        // Pastikan kelompok menu selalu ada saat seeder dijalankan ulang.
        DB::table('sys_gmenu')->updateOrInsert(
            ['gmenu' => 'rencan'],
            ['urut' => 3, 'name' => 'Perencanaan Pembelian', 'icon' => 'ni-cart', 'isactive' => '1']
        );

        foreach ([
            ['dmenu' => 'trpo', 'urut' => 1, 'name' => 'Pesanan Pembelian', 'url' => 'trpo', 'icon' => 'ni-cart', 'tabel' => 'pesanan_pembelian'],
            ['dmenu' => 'trump', 'urut' => 2, 'name' => 'Uang Muka Pembelian', 'url' => 'trump', 'icon' => 'ni-money-coins', 'tabel' => 'uang_muka_pembelian'],
        ] as $menu) {
            // Daftarkan dua menu transaksi ke dispatcher manual framework.
            DB::table('sys_dmenu')->updateOrInsert(
                ['dmenu' => $menu['dmenu']],
                ['gmenu' => 'rencan', 'layout' => 'manual', 'show' => '1', 'isactive' => '1', 'js' => '0'] + $menu
            );
            foreach (['admins', 'pembel'] as $role) {
                // Beri hak CRUD sesuai peran tanpa mengubah data akses lain.
                DB::table('sys_auth')->updateOrInsert(
                    ['idroles' => $role, 'dmenu' => $menu['dmenu']],
                    ['gmenu' => 'rencan', 'add' => '1', 'edit' => '1', 'delete' => '1', 'approval' => '0', 'value' => '0', 'print' => '0', 'excel' => '0', 'pdf' => '0', 'rules' => '0', 'isactive' => '1']
                );
            }
        }

        foreach ([
            ['dmenu' => 'trpo', 'field' => 'nomor', 'alias' => 'Nomor', 'type' => 'string', 'length' => 50, 'validate' => 'required|max:50', 'primary' => '1', 'list' => '1', 'show' => '1'],
            ['dmenu' => 'trpo', 'field' => 'tanggal', 'alias' => 'Tanggal', 'type' => 'date', 'length' => 10, 'validate' => 'required|date', 'primary' => '0', 'list' => '1', 'show' => '1'],
            ['dmenu' => 'trpo', 'field' => 'status', 'alias' => 'Status', 'type' => 'enum', 'length' => 15, 'validate' => '', 'primary' => '0', 'list' => '1', 'show' => '0'],
            ['dmenu' => 'trump', 'field' => 'nomor_form', 'alias' => 'Nomor Form', 'type' => 'string', 'length' => 50, 'validate' => 'required|max:50', 'primary' => '1', 'list' => '1', 'show' => '1'],
            ['dmenu' => 'trump', 'field' => 'tanggal', 'alias' => 'Tanggal', 'type' => 'date', 'length' => 10, 'validate' => 'required|date', 'primary' => '0', 'list' => '1', 'show' => '1'],
            ['dmenu' => 'trump', 'field' => 'status', 'alias' => 'Status', 'type' => 'enum', 'length' => 15, 'validate' => '', 'primary' => '0', 'list' => '1', 'show' => '0'],
        ] as $index => $field) {
            // Simpan metadata field agar konfigurasi menu tetap terdokumentasi di framework.
            DB::table('sys_table')->updateOrInsert(
                ['gmenu' => 'rencan', 'dmenu' => $field['dmenu'], 'field' => $field['field']],
                $field + ['urut' => $index + 1, 'filter' => '0', 'query' => '', 'default' => '', 'decimals' => '0', 'position' => '0', 'sub' => '', 'class' => '', 'note' => '', 'generateid' => '']
            );
        }
    }
}
