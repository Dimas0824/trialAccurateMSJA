<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PurchaseReportTableSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['rppo' => ['id', 'nomor', 'tanggal', 'nama_pemasok_snapshot', 'total_akhir', 'status'], 'rppnb' => ['id', 'nomor_form', 'tanggal_penerimaan', 'pemasok_id', 'status'], 'rpfkb' => ['id', 'nomor_form', 'tanggal_faktur', 'pemasok_id', 'total_akhir', 'jumlah_terutang', 'status'], 'rppby' => ['id', 'nomor_bukti', 'tanggal_pembayaran', 'pemasok_id', 'nilai_pembayaran', 'metode_bayar', 'status']] as $menu => $fields) {
            foreach ($fields as $urut => $field) {
                DB::table('sys_table')->updateOrInsert(['gmenu' => 'report', 'dmenu' => $menu, 'urut' => $urut + 1], ['field' => $field, 'alias' => ucwords(str_replace('_', ' ', $field)), 'type' => $field === 'id' ? 'hidden' : 'string', 'length' => 50, 'decimals' => '0', 'default' => '', 'validate' => '', 'primary' => $field === 'id' ? '1' : '0', 'filter' => '0', 'list' => $field === 'id' ? '0' : '1', 'show' => '0', 'query' => '', 'class' => '', 'sub' => '', 'link' => '', 'note' => '', 'position' => '0', 'isactive' => '1']);
            }
        }
    }
}
