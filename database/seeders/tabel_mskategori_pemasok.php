<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class tabel_mskategori_pemasok extends Seeder
{
    public function run(): void
    {
        DB::table('sys_table')->where(['gmenu' => 'master', 'dmenu' => 'mskatp'])->delete();

        $fields = [
            [1, 'id', 'ID', 'hidden', 20, '', 1, 0, 0, 0, ''],
            [2, 'nama', 'Nama Kategori', 'string', 100, 'required|max:100', 0, 1, 1, 1, ''],
            [3, 'adalah_default', 'Kategori Default', 'enum', 1, 'required|boolean', 0, 1, 1, 1, "select value, name from sys_enum where idenum = 'questions' and isactive = '1'"],
            [4, 'sub_kategori', 'Sub Kategori', 'enum', 1, 'required|boolean', 0, 0, 0, 1, "select value, name from sys_enum where idenum = 'questions' and isactive = '1'"],
            [5, 'kategori_induk_id', 'Kategori Induk', 'enum', 20, 'nullable|exists:kategori_pemasok,id', 0, 1, 1, 1, "select id as value, nama as name from kategori_pemasok where isactive = '1'"],
            [6, 'isactive', 'Status', 'enum', 1, '', 0, 0, 0, 0, "select value, name from sys_enum where idenum = 'isactive' and isactive = '1'"],
        ];

        foreach ($fields as [$urut, $field, $alias, $type, $length, $validate, $primary, $filter, $list, $show, $query]) {
            $primary = (string) $primary;
            $filter = (string) $filter;
            $list = (string) $list;
            $show = (string) $show;

            DB::table('sys_table')->insert(compact('urut', 'field', 'alias', 'type', 'length', 'validate', 'primary', 'filter', 'list', 'show', 'query') + [
                'gmenu' => 'master',
                'dmenu' => 'mskatp',
                'decimals' => '0',
                'default' => $field === 'adalah_default' || $field === 'sub_kategori' || $field === 'isactive' ? '1' : '',
            ]);
        }
    }
}
