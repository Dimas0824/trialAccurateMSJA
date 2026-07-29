<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kelompok_transfer_pemasok', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rekening_bank_perusahaan_id')->constrained('rekening_bank_perusahaan');
            $table->date('tanggal_transfer');
            $table->decimal('total_transfer', 18, 2)->default(0);
            $table->enum('status', ['draf', 'diekspor', 'dibayar', 'gagal'])->default('draf');
            $table->timestamp('diekspor_pada')->nullable();
            $table->timestamp('dibayar_pada')->nullable();
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
        Schema::dropIfExists('kelompok_transfer_pemasok');
    }
};
