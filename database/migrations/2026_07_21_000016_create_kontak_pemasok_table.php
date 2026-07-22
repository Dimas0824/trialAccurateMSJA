<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kontak_pemasok', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pemasok_id')->constrained('pemasok');
            $table->string('nama_lengkap', 150);
            $table->string('posisi_jabatan', 100)->nullable();
            $table->string('email', 150)->nullable();
            $table->string('nomor_handphone', 30)->nullable();
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
        Schema::dropIfExists('kontak_pemasok');
    }
};
