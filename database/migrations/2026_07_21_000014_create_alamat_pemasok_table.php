<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alamat_pemasok', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pemasok_id')->constrained('pemasok');
            $table->enum('tipe_alamat', ['pembayaran', 'pajak']);
            $table->text('jalan')->nullable();
            $table->string('kota', 100)->nullable();
            $table->string('kode_pos', 20)->nullable();
            $table->string('provinsi', 100)->nullable();
            $table->string('negara', 100)->nullable();
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
        Schema::dropIfExists('alamat_pemasok');
    }
};
