<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class menu_master_pemasok extends Seeder
{
    public function run(): void
    {
        DB::table('sys_auth')->where('idroles', 'admins')->whereIn('dmenu', ['mskatp', 'mspems'])->delete();

        DB::table('sys_gmenu')->updateOrInsert(
            ['gmenu' => 'master'],
            ['urut' => 2, 'name' => 'Master', 'icon' => 'ni-collection', 'isactive' => '1']
        );

        foreach ([
            ['dmenu' => 'mskatp', 'urut' => 2, 'name' => 'Kategori Pemasok', 'url' => 'mskatp', 'icon' => 'ni-tag', 'tabel' => 'kategori_pemasok', 'layout' => 'standr', 'js' => '1'],
            ['dmenu' => 'mspems', 'urut' => 3, 'name' => 'Pemasok', 'url' => 'mspems', 'icon' => 'ni-single-02', 'tabel' => 'pemasok', 'layout' => 'manual', 'js' => '0'],
        ] as $menu) {
            DB::table('sys_dmenu')->updateOrInsert(
                ['dmenu' => $menu['dmenu']],
                ['gmenu' => 'master', 'show' => '1', 'isactive' => '1'] + $menu
            );

            DB::table('sys_auth')->updateOrInsert(
                ['idroles' => 'pembel', 'dmenu' => $menu['dmenu']],
                ['gmenu' => 'master', 'add' => '1', 'edit' => '1', 'delete' => '1', 'approval' => '0', 'value' => '0', 'print' => '0', 'excel' => '0', 'pdf' => '0', 'rules' => '0', 'isactive' => '1']
            );
        }

        foreach ([
            ['value' => 'perorangan', 'name' => 'Perorangan'],
            ['value' => 'perusahaan', 'name' => 'Perusahaan'],
            ['value' => 'pemerintah', 'name' => 'Pemerintah'],
        ] as $type) {
            DB::table('sys_enum')->updateOrInsert(
                ['idenum' => 'tipe_pemasok', 'value' => $type['value']],
                ['name' => $type['name'], 'isactive' => '1']
            );
        }
    }
}
