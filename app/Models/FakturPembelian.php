<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FakturPembelian extends Model
{
    protected $table = 'faktur_pembelian';

    protected $fillable = ['pemasok_id', 'nomor_form', 'nomor_faktur_pemasok', 'tanggal_faktur', 'keterangan', 'subtotal', 'total_akhir', 'jumlah_terutang', 'status', 'user_create', 'user_update'];

    protected $casts = ['tanggal_faktur' => 'date', 'subtotal' => 'decimal:2', 'total_akhir' => 'decimal:2', 'jumlah_terutang' => 'decimal:2'];

    public function items(): HasMany
    {
        return $this->hasMany(RincianFakturPembelian::class);
    }
}
