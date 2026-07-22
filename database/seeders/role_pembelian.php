<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class role_pembelian extends Seeder
{
    public function run(): void
    {
        DB::table('sys_roles')->updateOrInsert(
            ['idroles' => 'pembel'],
            ['name' => 'Staff Pembelian', 'description' => 'Akses master pemasok dan kategori pemasok', 'isactive' => '1']
        );

        DB::table('users')->updateOrInsert(
            ['username' => 'pembelian'],
            [
                'firstname' => 'Staff',
                'lastname' => 'Pembelian',
                'email' => 'pembelian@local.test',
                'password' => Hash::make('pembelian'),
                'idroles' => 'pembel',
                'isactive' => '1',
            ]
        );
    }
}
