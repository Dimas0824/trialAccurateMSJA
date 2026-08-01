<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PurchaseReportGmenuSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('sys_gmenu')->updateOrInsert(['gmenu' => 'report'], ['urut' => 4, 'name' => 'Report', 'icon' => 'ni-single-copy-04', 'isactive' => '1']);
    }
}
