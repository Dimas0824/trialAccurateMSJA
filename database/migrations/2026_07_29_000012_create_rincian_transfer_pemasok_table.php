<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rincian_transfer_pemasok', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kelompok_transfer_pemasok_id')->constrained('kelompok_transfer_pemasok')->cascadeOnDelete();
            $table->foreignId('rincian_perintah_pembayaran_id')->constrained('rincian_perintah_pembayaran');
            $table->foreignId('rekening_bank_pemasok_id')->constrained('rekening_bank_pemasok');
            $table->decimal('jumlah_transfer', 18, 2);
            $table->string('nomor_referensi', 100)->nullable();
            $table->enum('status', ['menunggu', 'berhasil', 'gagal'])->default('menunggu');
            $table->timestamp('dibuat_pada')->useCurrent();
            $table->timestamp('diperbarui_pada')->useCurrent()->useCurrentOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rincian_transfer_pemasok');
    }
};
