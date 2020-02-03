<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table="tb_rating";
    protected $fillable = [
        'idPelanggan', 'idOwner', 'nilai'
    ];
}
