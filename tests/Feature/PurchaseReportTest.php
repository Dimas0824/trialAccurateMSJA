<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class PurchaseReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_purchase_report_data_requires_menu_access_and_filters_purchase_orders(): void
    {
        DB::table('sys_roles')->insert(['idroles' => 'admins', 'name' => 'Admin', 'description' => 'Administrator']);
        DB::table('sys_roles')->insert(['idroles' => 'guestx', 'name' => 'Guest', 'description' => 'Tanpa akses report']);
        DB::table('sys_gmenu')->insert(['gmenu' => 'report', 'urut' => 4, 'name' => 'Report', 'icon' => 'ni-single-copy-04']);
        DB::table('sys_dmenu')->insert(['gmenu' => 'report', 'dmenu' => 'rppo', 'urut' => 2, 'name' => 'Laporan Pesanan Pembelian', 'url' => 'rppo', 'icon' => 'ni-cart', 'tabel' => 'pesanan_pembelian', 'layout' => 'manual']);
        DB::table('sys_auth')->insert(['idroles' => 'admins', 'gmenu' => 'report', 'dmenu' => 'rppo', 'add' => '0', 'edit' => '0', 'delete' => '0', 'print' => '1', 'excel' => '1', 'pdf' => '1']);

        $supplierId = DB::table('pemasok')->insertGetId(['kode_pemasok' => 'SUP-01', 'nama' => 'Pemasok Utama']);
        DB::table('pesanan_pembelian')->insert([
            'pemasok_id' => $supplierId,
            'nomor' => 'PO-AGUSTUS',
            'tanggal' => now()->startOfMonth()->toDateString(),
            'kode_pemasok_snapshot' => 'SUP-01',
            'nama_pemasok_snapshot' => 'Pemasok Utama',
            'total_akhir' => 150000,
            'status' => 'terbuka',
        ]);

        $guest = User::create(['username' => 'guest-report', 'email' => 'guest-report@example.test', 'password' => 'secret', 'idroles' => 'guestx']);
        $this->actingAs($guest)->withSession(['username' => $guest->username])
            ->getJson('/report-pembelian/rppo/data')
            ->assertForbidden();

        $admin = User::create(['username' => 'admin-report', 'email' => 'admin-report@example.test', 'password' => 'secret', 'idroles' => 'admins']);
        $this->actingAs($admin)->withSession(['username' => $admin->username])
            ->getJson('/report-pembelian/rppo/data?status=terbuka&length=25')
            ->assertOk()
            ->assertJsonPath('recordsFiltered', 1)
            ->assertJsonPath('data.0.nomor', 'PO-AGUSTUS')
            ->assertJsonPath('summary.document_count', 1);

        $this->actingAs($admin)->get('/report-pembelian/rppo/export/pdf?currency_id=')->assertOk()->assertHeader('content-type', 'application/pdf')->assertSee('%PDF-', false);
    }
}
