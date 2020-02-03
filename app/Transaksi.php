<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table="tb_transaksi";
    protected $fillable = [
        'idTransaksi', 'idProduk', 'idPelanggan','gambar'
    ];
}
