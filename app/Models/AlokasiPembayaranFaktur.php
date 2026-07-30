<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AlokasiPembayaranFaktur extends Model
{
    protected $table = 'alokasi_pembayaran_faktur';

    public $timestamps = false;

    protected $fillable = ['faktur_pembelian_id', 'total_faktur', 'jumlah_terutang', 'jumlah_bayar', 'jumlah_diskon', 'jumlah_dialokasikan'];
}
