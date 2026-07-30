<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penerimaan_barang', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pemasok_id')->constrained('pemasok');
            $table->foreignId('urutan_penomoran_id')->nullable()->constrained('urutan_penomoran');
            $table->foreignId('metode_pengiriman_id')->nullable()->constrained('metode_pengiriman');
            $table->foreignId('ketentuan_fob_id')->nullable()->constrained('ketentuan_fob');
            $table->string('nomor_form', 50)->unique();
            $table->date('tanggal_penerimaan');
            $table->string('nomor_penerimaan_pemasok', 50)->nullable();
            $table->text('alamat')->nullable();
            $table->text('keterangan')->nullable();
            $table->date('tanggal_pengiriman')->nullable();
            $table->enum('status', ['draf', 'diterima', 'difakturkan', 'dibatalkan'])->default('draf');
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
        Schema::dropIfExists('penerimaan_barang');
    }
};
