<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('akun_perkiraan', function (Blueprint $table) {
            $table->id();
            $table->string('kode_akun', 30)->unique();
            $table->string('nama_akun', 150);
            $table->string('tipe_akun', 50);
            $table->foreignId('akun_induk_id')->nullable()->constrained('akun_perkiraan');
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
        Schema::dropIfExists('akun_perkiraan');
    }
};
