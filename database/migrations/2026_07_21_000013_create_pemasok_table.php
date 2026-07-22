<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pemasok', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kategori_pemasok_id')->nullable()->constrained('kategori_pemasok');
            $table->foreignId('urutan_penomoran_id')->nullable()->constrained('urutan_penomoran');
            $table->string('kode_pemasok', 50)->unique();
            $table->boolean('penomoran_otomatis')->default(false);
            $table->string('nama', 150);
            $table->string('telepon_bisnis', 30)->nullable();
            $table->string('nomor_handphone', 30)->nullable();
            $table->string('nomor_whatsapp', 30)->nullable();
            $table->string('email', 150)->nullable();
            $table->string('nomor_faksimili', 30)->nullable();
            $table->string('situs_web', 255)->nullable();
            $table->boolean('penjual_jasa_orang_pribadi')->default(false);
            $table->enum('tipe_pemasok', ['perorangan', 'perusahaan', 'pemerintah'])->nullable();
            $table->foreignId('syarat_pembayaran_id')->nullable()->constrained('syarat_pembayaran');
            $table->decimal('diskon_default_persen', 7, 4)->default(0);
            $table->text('deskripsi_default')->nullable();
            $table->foreignId('akun_pembelian_id')->nullable()->constrained('akun_perkiraan');
            $table->foreignId('akun_utang_id')->nullable()->constrained('akun_perkiraan');
            $table->foreignId('akun_uang_muka_id')->nullable()->constrained('akun_perkiraan');
            $table->boolean('memberikan_nomor_faktur')->default(false);
            $table->text('catatan')->nullable();
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
        Schema::dropIfExists('pemasok');
    }
};
