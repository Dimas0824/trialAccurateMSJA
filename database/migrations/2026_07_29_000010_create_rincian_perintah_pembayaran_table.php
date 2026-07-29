<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rincian_perintah_pembayaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('perintah_pembayaran_id')->constrained('perintah_pembayaran')->cascadeOnDelete();
            $table->foreignId('faktur_pembelian_id')->constrained('faktur_pembelian');
            $table->foreignId('rekening_bank_pemasok_id')->nullable()->constrained('rekening_bank_pemasok');
            $table->decimal('total_faktur', 18, 2);
            $table->decimal('jumlah_terutang', 18, 2);
            $table->decimal('jumlah_bayar', 18, 2);
            $table->decimal('jumlah_diskon', 18, 2)->default(0);
            $table->decimal('jumlah_dialokasikan', 18, 2);
            $table->unique(['perintah_pembayaran_id', 'faktur_pembelian_id'], 'uq_perintah_faktur');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rincian_perintah_pembayaran');
    }
};
