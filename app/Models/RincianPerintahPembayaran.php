<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RincianPerintahPembayaran extends Model
{
    protected $table = 'rincian_perintah_pembayaran';

    public $timestamps = false;

    protected $fillable = ['faktur_pembelian_id', 'rekening_bank_pemasok_id', 'total_faktur', 'jumlah_terutang', 'jumlah_bayar', 'jumlah_diskon', 'jumlah_dialokasikan'];
}
