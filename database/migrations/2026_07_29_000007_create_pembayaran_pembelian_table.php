<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pembayaran_pembelian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pemasok_id')->constrained('pemasok');
            $table->foreignId('akun_bank_id')->constrained('akun_perkiraan');
            $table->foreignId('mata_uang_id')->nullable()->constrained('mata_uang');
            $table->foreignId('urutan_penomoran_id')->nullable()->constrained('urutan_penomoran');
            $table->string('nomor_bukti', 50)->unique();
            $table->date('tanggal_pembayaran');
            $table->decimal('nilai_pembayaran', 18, 2);
            $table->enum('metode_bayar', ['tunai', 'cek_giro', 'transfer_bank', 'edc', 'kartu_debit', 'kartu_kredit', 'qris', 'tautan_pembayaran', 'virtual_account', 'dompet_digital', 'non_tunai_lainnya']);
            $table->text('keterangan')->nullable();
            $table->enum('status', ['draf', 'diposting', 'dibatalkan'])->default('draf');
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
        Schema::dropIfExists('pembayaran_pembelian');
    }
};
