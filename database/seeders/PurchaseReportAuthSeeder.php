<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PurchaseReportAuthSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['rppo', 'rppnb', 'rpfkb', 'rppby'] as $menu) {
            foreach (['admins', 'pembel'] as $role) {
                DB::table('sys_auth')->updateOrInsert(['idroles' => $role, 'dmenu' => $menu], ['gmenu' => 'report', 'add' => '0', 'edit' => '0', 'delete' => '0', 'approval' => '0', 'value' => '0', 'print' => '1', 'excel' => '1', 'pdf' => '1', 'rules' => '0', 'isactive' => '1']);
            }
        }
    }
}
