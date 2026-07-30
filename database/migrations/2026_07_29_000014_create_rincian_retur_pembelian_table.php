<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rincian_retur_pembelian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('retur_pembelian_id')->constrained('retur_pembelian')->cascadeOnDelete();
            $table->string('barang_jasa_kode', 50);
            $table->foreign('barang_jasa_kode')->references('kode_barang')->on('barang_jasa');
            $table->foreignId('rincian_faktur_pembelian_id')->nullable()->constrained('rincian_faktur_pembelian');
            $table->foreignId('rincian_penerimaan_barang_id')->nullable()->constrained('rincian_penerimaan_barang');
            $table->foreignId('pajak_id')->nullable()->constrained('pajak');
            $table->string('nama_barang_snapshot', 150);
            $table->string('satuan_snapshot', 100);
            $table->decimal('kuantitas', 18, 4);
            $table->decimal('harga_satuan', 18, 2)->default(0);
            $table->decimal('jumlah_diskon', 18, 2)->default(0);
            $table->decimal('total_baris', 18, 2)->default(0);
            $table->text('keterangan')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rincian_retur_pembelian');
    }
};
