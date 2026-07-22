<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('profil_pajak_pemasok', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pemasok_id')->unique()->constrained('pemasok');
            $table->boolean('faktur_default_termasuk_pajak')->default(false);
            $table->string('tipe_identitas_pajak', 30)->nullable();
            $table->string('nomor_wajib_pajak', 50)->nullable();
            $table->string('nama_wajib_pajak', 150)->nullable();
            $table->string('nitku', 30)->nullable();
            $table->enum('tipe_transaksi', ['faktur_pajak', 'impor', 'perolehan_dalam_negeri', 'tidak_dikreditkan', 'ditanggung_pemerintah'])->nullable();
            $table->boolean('alamat_pajak_sama_dengan_pembayaran')->default(true);
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
        Schema::dropIfExists('profil_pajak_pemasok');
    }
};
