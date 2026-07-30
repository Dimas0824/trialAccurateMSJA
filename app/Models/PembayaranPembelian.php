<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PembayaranPembelian extends Model
{
    protected $table = 'pembayaran_pembelian';

    protected $fillable = ['pemasok_id', 'akun_bank_id', 'nomor_bukti', 'tanggal_pembayaran', 'nilai_pembayaran', 'metode_bayar', 'keterangan', 'status', 'user_create', 'user_update'];

    protected $casts = ['tanggal_pembayaran' => 'date', 'nilai_pembayaran' => 'decimal:2'];

    public function allocations(): HasMany
    {
        return $this->hasMany(AlokasiPembayaranFaktur::class);
    }
}
