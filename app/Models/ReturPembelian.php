<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReturPembelian extends Model
{
    protected $table = 'retur_pembelian';

    protected $fillable = ['pemasok_id', 'faktur_pembelian_id', 'penerimaan_barang_id', 'nomor_retur', 'tanggal_retur', 'sumber_retur', 'keterangan', 'subtotal', 'total_akhir', 'status', 'user_create', 'user_update'];

    protected $casts = ['tanggal_retur' => 'date', 'subtotal' => 'decimal:2', 'total_akhir' => 'decimal:2'];

    public function items(): HasMany
    {
        return $this->hasMany(RincianReturPembelian::class);
    }
}
