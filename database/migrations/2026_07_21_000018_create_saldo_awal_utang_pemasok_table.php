<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('saldo_awal_utang_pemasok', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pemasok_id')->constrained('pemasok');
            $table->foreignId('mata_uang_id')->constrained('mata_uang');
            $table->foreignId('syarat_pembayaran_id')->nullable()->constrained('syarat_pembayaran');
            $table->date('tanggal');
            $table->decimal('jumlah', 18, 2);
            $table->string('nomor_dokumen', 50)->nullable();
            $table->text('keterangan')->nullable();
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
        Schema::dropIfExists('saldo_awal_utang_pemasok');
    }
};
