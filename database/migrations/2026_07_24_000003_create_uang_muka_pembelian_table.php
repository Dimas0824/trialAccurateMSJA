<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Simpan uang muka tanpa memengaruhi stok barang.
        Schema::create('uang_muka_pembelian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pemasok_id')->constrained('pemasok');
            $table->foreignId('pesanan_pembelian_id')->nullable()->constrained('pesanan_pembelian');
            $table->foreignId('mata_uang_id')->nullable()->constrained('mata_uang');
            $table->foreignId('urutan_penomoran_id')->nullable()->constrained('urutan_penomoran');
            $table->foreignId('rekening_bank_pemasok_id')->nullable()->constrained('rekening_bank_pemasok');
            $table->foreignId('syarat_pembayaran_id')->nullable()->constrained('syarat_pembayaran');
            $table->string('nomor_form', 50)->unique();
            $table->date('tanggal');
            $table->string('nomor_faktur_pemasok', 50);
            // Snapshot pemasok menjaga histori pembayaran tetap akurat.
            $table->string('kode_pemasok_snapshot', 50);
            $table->string('nama_pemasok_snapshot', 150);
            $table->text('alamat')->nullable();
            $table->text('keterangan')->nullable();
            $table->decimal('jumlah_uang_muka', 18, 2);
            $table->boolean('kena_pajak')->default(false);
            $table->boolean('total_termasuk_pajak')->default(false);
            $table->decimal('subtotal', 18, 2)->default(0);
            $table->decimal('jumlah_pajak', 18, 2)->default(0);
            $table->decimal('total_akhir', 18, 2)->default(0);
            $table->enum('status', ['draf', 'terbuka', 'digunakan', 'dibatalkan'])->default('draf');
            $table->timestamp('dibuat_pada')->useCurrent();
            $table->timestamp('diperbarui_pada')->useCurrent()->useCurrentOnUpdate();
            $table->timestamps();
            $table->string('user_create')->nullable();
            $table->string('user_update')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('uang_muka_pembelian');
    }
};
