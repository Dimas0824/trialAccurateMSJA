<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class menu_pembelian_lanjutan extends Seeder
{
    public function run(): void
    {
        foreach ([
            ['gmenu' => 'tertag', 'urut' => 4, 'name' => 'Penerimaan dan Tagihan', 'icon' => 'ni-box-2'],
            ['gmenu' => 'bayarp', 'urut' => 5, 'name' => 'Pembayaran Pembelian', 'icon' => 'ni-money-coins'],
            ['gmenu' => 'koreks', 'urut' => 6, 'name' => 'Koreksi Pembelian', 'icon' => 'ni-collection'],
        ] as $group) {
            // Pastikan kelompok menu tersedia tanpa menghapus konfigurasi lain.
            DB::table('sys_gmenu')->updateOrInsert(
                ['gmenu' => $group['gmenu']],
                $group + ['isactive' => '1']
            );
        }

        foreach ([
            ['gmenu' => 'tertag', 'dmenu' => 'trpnb', 'urut' => 1, 'name' => 'Penerimaan Barang', 'url' => 'trpnb', 'icon' => 'ni-box-2', 'tabel' => 'penerimaan_barang'],
            ['gmenu' => 'tertag', 'dmenu' => 'trfkb', 'urut' => 2, 'name' => 'Faktur Pembelian', 'url' => 'trfkb', 'icon' => 'ni-single-copy-04', 'tabel' => 'faktur_pembelian'],
            ['gmenu' => 'bayarp', 'dmenu' => 'trpby', 'urut' => 1, 'name' => 'Pembayaran Pembelian', 'url' => 'trpby', 'icon' => 'ni-money-coins', 'tabel' => 'pembayaran_pembelian'],
            ['gmenu' => 'bayarp', 'dmenu' => 'trprby', 'urut' => 2, 'name' => 'Perintah Pembayaran', 'url' => 'trprby', 'icon' => 'ni-check-bold', 'tabel' => 'perintah_pembayaran'],
            ['gmenu' => 'bayarp', 'dmenu' => 'trtrfp', 'urut' => 3, 'name' => 'Transfer Pemasok', 'url' => 'trtrfp', 'icon' => 'ni-send', 'tabel' => 'kelompok_transfer_pemasok'],
            ['gmenu' => 'koreks', 'dmenu' => 'trretp', 'urut' => 1, 'name' => 'Retur Pembelian', 'url' => 'trretp', 'icon' => 'ni-undo-2', 'tabel' => 'retur_pembelian'],
        ] as $menu) {
            // Daftarkan navigasi transaksi; implementasi dokumen dan halaman menyusul per menu.
            DB::table('sys_dmenu')->updateOrInsert(
                ['dmenu' => $menu['dmenu']],
                $menu + ['layout' => 'manual', 'show' => '1', 'js' => '0', 'isactive' => '1']
            );

            foreach (['admins', 'pembel'] as $role) {
                // Beri akses menu untuk administrator dan staf pembelian.
                DB::table('sys_auth')->updateOrInsert(
                    ['idroles' => $role, 'dmenu' => $menu['dmenu']],
                    ['gmenu' => $menu['gmenu'], 'add' => '1', 'edit' => '1', 'delete' => '1', 'approval' => '0', 'value' => '0', 'print' => '0', 'excel' => '0', 'pdf' => '0', 'rules' => '0', 'isactive' => '1']
                );
            }
        }
    }
}
