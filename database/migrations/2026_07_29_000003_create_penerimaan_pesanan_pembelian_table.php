<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penerimaan_pesanan_pembelian', function (Blueprint $table) {
            $table->foreignId('penerimaan_barang_id')->constrained('penerimaan_barang')->cascadeOnDelete();
            $table->foreignId('pesanan_pembelian_id')->constrained('pesanan_pembelian');
            $table->primary(['penerimaan_barang_id', 'pesanan_pembelian_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penerimaan_pesanan_pembelian');
    }
};
