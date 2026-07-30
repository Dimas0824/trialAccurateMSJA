<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KelompokTransferPemasok extends Model
{
    protected $table = 'kelompok_transfer_pemasok';

    protected $fillable = ['rekening_bank_perusahaan_id', 'tanggal_transfer', 'total_transfer', 'status', 'user_create', 'user_update'];

    protected $casts = ['tanggal_transfer' => 'date', 'total_transfer' => 'decimal:2'];

    public function items(): HasMany
    {
        return $this->hasMany(RincianTransferPemasok::class);
    }
}
