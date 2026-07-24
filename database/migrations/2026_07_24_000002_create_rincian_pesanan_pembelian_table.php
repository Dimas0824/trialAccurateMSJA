<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Simpan setiap barang dari pesanan secara terpisah dari header.
        Schema::create('rincian_pesanan_pembelian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pesanan_pembelian_id')->constrained('pesanan_pembelian')->cascadeOnDelete();
            // The existing master uses kode_barang as its primary key.
            $table->string('barang_jasa_kode', 50);
            $table->foreignId('pajak_id')->nullable()->constrained('pajak');
            // Snapshot barang menjaga histori saat master barang diperbarui.
            $table->string('kode_barang_snapshot', 50);
            $table->string('nama_barang_snapshot', 150);
            $table->string('nama_satuan_snapshot', 100)->nullable();
            $table->decimal('kuantitas', 18, 4);
            $table->decimal('harga_satuan', 18, 2);
            $table->decimal('diskon_persen', 7, 4)->default(0);
            $table->decimal('jumlah_diskon', 18, 2)->default(0);
            $table->decimal('total_baris', 18, 2);
            $table->text('keterangan')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rincian_pesanan_pembelian');
    }
};
