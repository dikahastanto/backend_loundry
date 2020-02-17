<?php

namespace App\Http\Controllers;
use App\Rating;
use App\User;
use App\Transaksi;
use Illuminate\Http\Request;

class RatingController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }
    public function giveRating (Request $request) {
        $insert = Rating::insert($request->all());
        $response;
        if ($insert) {
            // update total rating
            $nilaiRating = $this->getRating($request->idOwner);
            User::where('id', $request->idOwner)->update(['totalRating' => $nilaiRating]);
            $response = [
                'sukses' => true,
                'msg' => 'Berhasil Memberi Rating'
            ];
        } else {
            $response = [
                'sukses' => false,
                'msg' => 'Terjadi Kesalahan Saat Memberi Rating'
            ];
        }
        return $response;
    }

    public function getStatusRating ($idOwner, $idPelanggan)
    {
        $cekRating = Rating::where([
            ['idPelanggan', '=', $idPelanggan],
            ['idOwner', '=', $idOwner]
        ])->first();
        $cekTransaksi = Transaksi::join('users', 'tb_transaksi.idPelanggan', '=', 'users.id')
                                        ->join('tb_produk', 'tb_transaksi.idProduk', '=', 'tb_produk.id')
                                        ->join('users as owner', 'tb_produk.idUser', '=', 'owner.id')
                                        ->select(
                                            'tb_transaksi.idTransaksi',
                                            'tb_transaksi.status',
                                            'tb_transaksi.created_at',
                                            'users.namaLengkap as namaPelanggan',
                                            'tb_produk.id as idProduk',
                                            'tb_produk.nama as namaProduk',
                                            'tb_produk.harga',
                                            'users.email',
                                            'tb_produk.isi',
                                            'tb_transaksi.gambar as gambar'
                                            )
                                        ->where([
                                            ['tb_transaksi.idPelanggan', '=', $idPelanggan],
                                            ['tb_produk.idUser', '=', $idOwner]
                                        ])
                                        ->first();
        $response = [
            'sudahRating' => $cekRating == null ? false : true,
            'pernahTransaksi' => $cekTransaksi == null ? false : true,
            'nilaiRating' => $this->getRating($idOwner)
        ];
        return $response;
    }
    //
    public function getRating ($idOwner) {
        $nilai = Rating::where([
            ['idOwner', $idOwner]
        ])->sum('nilai');

        $jumlah = Rating::where([
            ['idOwner', $idOwner]
        ])->count();

        if ($jumlah) {
            return round($nilai / $jumlah, 1);
        } else {
            return 0;
        }
    }
}
