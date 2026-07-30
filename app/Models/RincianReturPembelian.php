<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RincianReturPembelian extends Model
{
    protected $table = 'rincian_retur_pembelian';

    public $timestamps = false;

    protected $fillable = ['barang_jasa_kode', 'rincian_faktur_pembelian_id', 'rincian_penerimaan_barang_id', 'nama_barang_snapshot', 'satuan_snapshot', 'kuantitas', 'harga_satuan', 'jumlah_diskon', 'total_baris', 'keterangan'];
}
