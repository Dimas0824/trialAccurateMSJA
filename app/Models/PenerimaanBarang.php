<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PenerimaanBarang extends Model
{
    protected $table = 'penerimaan_barang';

    protected $fillable = ['pemasok_id', 'nomor_form', 'tanggal_penerimaan', 'nomor_penerimaan_pemasok', 'keterangan', 'status', 'user_create', 'user_update'];

    protected $casts = ['tanggal_penerimaan' => 'date'];

    public function items(): HasMany
    {
        return $this->hasMany(RincianPenerimaanBarang::class);
    }
}
