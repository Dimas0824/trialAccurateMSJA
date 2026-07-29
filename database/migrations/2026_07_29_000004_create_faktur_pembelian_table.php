<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('faktur_pembelian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pemasok_id')->constrained('pemasok');
            $table->foreignId('mata_uang_id')->nullable()->constrained('mata_uang');
            $table->foreignId('urutan_penomoran_id')->nullable()->constrained('urutan_penomoran');
            $table->foreignId('syarat_pembayaran_id')->nullable()->constrained('syarat_pembayaran');
            $table->foreignId('rekening_bank_pemasok_id')->nullable()->constrained('rekening_bank_pemasok');
            $table->foreignId('metode_pengiriman_id')->nullable()->constrained('metode_pengiriman');
            $table->foreignId('ketentuan_fob_id')->nullable()->constrained('ketentuan_fob');
            $table->string('nomor_form', 50)->unique();
            $table->date('tanggal_faktur');
            $table->string('nomor_faktur_pemasok', 50);
            $table->text('alamat')->nullable();
            $table->text('keterangan')->nullable();
            $table->boolean('kena_pajak')->default(false);
            $table->boolean('total_termasuk_pajak')->default(false);
            $table->date('tanggal_pengiriman')->nullable();
            $table->decimal('subtotal', 18, 2)->default(0);
            $table->decimal('jumlah_diskon', 18, 2)->default(0);
            $table->decimal('jumlah_pajak', 18, 2)->default(0);
            $table->decimal('total_biaya_lainnya', 18, 2)->default(0);
            $table->decimal('total_akhir', 18, 2)->default(0);
            $table->decimal('jumlah_terutang', 18, 2)->default(0);
            $table->enum('status', ['draf', 'terutang', 'dibayar_sebagian', 'lunas', 'dibatalkan'])->default('draf');
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
        Schema::dropIfExists('faktur_pembelian');
    }
};
