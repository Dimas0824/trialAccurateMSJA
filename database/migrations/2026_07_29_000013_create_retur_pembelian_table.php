<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('retur_pembelian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pemasok_id')->constrained('pemasok');
            $table->foreignId('urutan_penomoran_id')->nullable()->constrained('urutan_penomoran');
            $table->foreignId('faktur_pembelian_id')->nullable()->constrained('faktur_pembelian');
            $table->foreignId('penerimaan_barang_id')->nullable()->constrained('penerimaan_barang');
            $table->string('nomor_retur', 50)->unique();
            $table->date('tanggal_retur');
            $table->enum('sumber_retur', ['faktur', 'penerimaan_barang']);
            $table->text('alamat_tujuan')->nullable();
            $table->text('keterangan')->nullable();
            $table->string('catatan_cetak_email', 255)->nullable();
            $table->boolean('kena_pajak')->default(false);
            $table->boolean('total_termasuk_pajak')->default(false);
            $table->decimal('subtotal', 18, 2)->default(0);
            $table->decimal('jumlah_diskon', 18, 2)->default(0);
            $table->decimal('jumlah_pajak', 18, 2)->default(0);
            $table->decimal('total_biaya_lainnya', 18, 2)->default(0);
            $table->decimal('total_akhir', 18, 2)->default(0);
            $table->enum('status', ['draf', 'diposting', 'dibatalkan'])->default('draf');
            $table->timestamp('dibuat_pada')->useCurrent();
            $table->timestamp('diperbarui_pada')->useCurrent()->useCurrentOnUpdate();
            $table->enum('isactive', [0, 1])->default(1);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->string('user_create')->nullable();
            $table->string('user_update')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('retur_pembelian');
    }
};
