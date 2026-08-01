<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PurchaseReportDmenuSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([['rppo', 'Pesanan Pembelian', 'pesanan_pembelian'], ['rppnb', 'Penerimaan Barang', 'penerimaan_barang'], ['rpfkb', 'Faktur Pembelian', 'faktur_pembelian'], ['rppby', 'Pembayaran Pembelian', 'pembayaran_pembelian']] as $i => $r) {
            DB::table('sys_dmenu')->updateOrInsert(['dmenu' => $r[0]], ['gmenu' => 'report', 'urut' => $i + 2, 'name' => $r[1], 'url' => $r[0], 'icon' => 'ni-single-copy-04', 'tabel' => $r[2], 'layout' => 'manual', 'show' => '1', 'js' => '0', 'isactive' => '1']);
        }
    }
}
