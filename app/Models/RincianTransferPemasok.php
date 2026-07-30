<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RincianTransferPemasok extends Model
{
    protected $table = 'rincian_transfer_pemasok';

    protected $fillable = ['rincian_perintah_pembayaran_id', 'rekening_bank_pemasok_id', 'jumlah_transfer', 'nomor_referensi', 'status'];
}
