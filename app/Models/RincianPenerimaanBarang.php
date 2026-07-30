<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RincianPenerimaanBarang extends Model
{
    protected $table = 'rincian_penerimaan_barang';

    public $timestamps = false;

    protected $fillable = ['barang_jasa_kode', 'rincian_pesanan_pembelian_id', 'nama_barang_snapshot', 'satuan_snapshot', 'kuantitas', 'keterangan'];
}
