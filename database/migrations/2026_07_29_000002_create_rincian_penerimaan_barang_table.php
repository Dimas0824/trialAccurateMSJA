<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rincian_penerimaan_barang', function (Blueprint $table) {
            $table->id();
            $table->foreignId('penerimaan_barang_id')->constrained('penerimaan_barang')->cascadeOnDelete();
            // Master barang aktif memakai kode_barang sebagai primary key, bukan ID numerik DBML lama.
            $table->string('barang_jasa_kode', 50);
            $table->foreign('barang_jasa_kode')->references('kode_barang')->on('barang_jasa');
            $table->foreignId('rincian_pesanan_pembelian_id')->nullable()->constrained('rincian_pesanan_pembelian');
            $table->string('nama_barang_snapshot', 150);
            $table->string('satuan_snapshot', 100);
            $table->decimal('kuantitas', 18, 4);
            $table->text('keterangan')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rincian_penerimaan_barang');
    }
};
