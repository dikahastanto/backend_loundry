<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table="tb_produk";
    protected $fillable = [
        'nama', 'harga', 'isi', 'gambar', 'idUser'
    ];
}
