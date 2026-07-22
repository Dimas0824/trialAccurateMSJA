<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('barang_jasa', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 50)->unique();
            $table->string('nama', 150);
            $table->foreignId('satuan_default_id')->nullable()->constrained('satuan');
            $table->foreignId('akun_pembelian_id')->nullable()->constrained('akun_perkiraan');
            $table->foreignId('akun_persediaan_id')->nullable()->constrained('akun_perkiraan');
            $table->boolean('adalah_jasa')->default(false);
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
        Schema::dropIfExists('barang_jasa');
    }
};
