<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // MySQL dapat menyisakan tabel saat pembuatan index gagal; retry cukup melengkapi index.
        if (Schema::hasTable('alokasi_pembayaran_faktur')) {
            Schema::table('alokasi_pembayaran_faktur', fn (Blueprint $table) => $table->unique(['pembayaran_pembelian_id', 'faktur_pembelian_id'], 'uq_bayar_faktur'));

            return;
        }

        Schema::create('alokasi_pembayaran_faktur', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pembayaran_pembelian_id')->constrained('pembayaran_pembelian')->cascadeOnDelete();
            $table->foreignId('faktur_pembelian_id')->constrained('faktur_pembelian');
            $table->decimal('total_faktur', 18, 2);
            $table->decimal('jumlah_terutang', 18, 2);
            $table->decimal('jumlah_bayar', 18, 2);
            $table->decimal('jumlah_diskon', 18, 2)->default(0);
            $table->decimal('jumlah_dialokasikan', 18, 2);
            $table->unique(['pembayaran_pembelian_id', 'faktur_pembelian_id'], 'uq_bayar_faktur');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alokasi_pembayaran_faktur');
    }
};
