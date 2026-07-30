<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PerintahPembayaran extends Model
{
    protected $table = 'perintah_pembayaran';

    protected $fillable = ['nomor_bukti', 'tanggal_batas_transfer', 'metode_bayar', 'keterangan', 'status', 'user_create', 'user_update'];

    protected $casts = ['tanggal_batas_transfer' => 'date'];

    public function items(): HasMany
    {
        return $this->hasMany(RincianPerintahPembayaran::class);
    }
}
