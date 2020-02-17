<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Transaksi;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;

class TransaksiController extends Controller
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

    public function insert (Request $request) {
      $response = null;
      if ($request->hasFile('image')) {
        $messages = [
          'required' => 'Harap Isi :attribute Anda',
          'mimes' => 'Format Gambar Harus jpeg, png, jpg',
          'max' => 'Gambar Tidak Boleh Lebih Dari 2 Mb'
        ];
        $rulesGambar = [
          'image' => 'required|mimes:jpeg,png,jpg|max:2048',
        ];
        $validator = Validator::make($request->all(), $rulesGambar, $messages);
        if ($validator->fails()) {
          $response = [
            'sukses' => false,
            'msg' => 'Format Gambar Tidak Valid'
          ];
        } else {

          $file = $request->file('image');
          $name = Str::random(32) . round(microtime(true)) . '.' . $file->guessExtension();

          if ($file->move('img/transaksi', $name)) {
              $data = [
                'idTransaksi' => 'LTX-' . Str::random(10),
                'idProduk' => $request->idProduk,
                'idPelanggan' => $request->idPelanggan,
                'gambar' => $name,
              ];
              $insert = Transaksi::create($data);
              if ($insert) {
                $response = [
                  'sukses' => true,
                  'msg' => 'Berhasil Memesan'
                ];
              } else {
                $response = [
                  'sukses' => false,
                  'msg' => 'Terjadi Kesalahan Saat Menyimpan Data'
                ];
              }
          } else {
            $response = [
              'sukses' => false,
              'msg' => 'Terjadi Kesalahan Saat Mengunggah Gambar'
            ];
          }
        }
      } else {
        $response = [
          'sukses' => false,
          'msg' => 'Mohon Upload Gambar'
        ];
      }
      return $response;
    }

    public function getPelanggan ($id) {
      $produk = Transaksi::join('users', 'tb_transaksi.idPelanggan', '=', 'users.id')
                            ->join('tb_produk', 'tb_transaksi.idProduk', '=', 'tb_produk.id')
                            ->join('users as owner', 'tb_produk.idUser', '=', 'owner.id')
                            ->select(
                                'tb_transaksi.idTransaksi',
                                'tb_transaksi.status',
                                'tb_transaksi.created_at',
                                'owner.namaLengkap',
                                'tb_produk.id as idProduk',
                                'tb_produk.nama as namaProduk',
                                'tb_produk.harga',
                                'tb_produk.isi'
                                )
                            ->where('tb_transaksi.idPelanggan', '=', $id)
                            ->orderBy('tb_transaksi.status', 'asc')
                            ->get();
      return response()->json($produk);
    }
    public function getTransaksiByOwner ($id) {
        $produk = Transaksi::join('users', 'tb_transaksi.idPelanggan', '=', 'users.id')
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
                                'tb_produk.isi',
                                'tb_transaksi.gambar as gambar'
                                )
                            ->where([
                              ['tb_produk.idUser', '=', $id],
                              ['tb_transaksi.status', '<>', 3]
                            ])
                            ->get();
        return response()->json($produk);
    }

    public function selesai ($id) {
      $data = Transaksi::join('users', 'tb_transaksi.idPelanggan', '=', 'users.id')
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
                          ->where('tb_transaksi.idTransaksi', '=', $id)
                          ->first();
      $mail['to'] = $data['email'];
        $mail['subject'] ="Pesanan Telah Selesai";
        
      $update = Transaksi::where('idTransaksi', '=', $id)
                          ->update(
                            ['status' => 3]
                          );
      $response = null;
      if ($update) {
        try{
          Mail::send('selesai',['data' => $data] ,function($message)use($mail) {
          $message->to($mail['to'], '')
          ->subject($mail['subject']);
          });
        }catch(JWTException $exception){
            $this->serverstatuscode = "0";
            $this->serverstatusdes = $exception->getMessage();
        }
        $response = [
          'sukses' => true,
          'msg' => 'Pesanan Telah Selesai'
        ];
      } else {
        $response = [
          'sukses' => false,
          'msg' => 'Ada Kesalahan'
        ];
      }
      return $response;
    }
    public function confirm ($id) {
      $data = Transaksi::join('users', 'tb_transaksi.idPelanggan', '=', 'users.id')
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
                          ->where('tb_transaksi.idTransaksi', '=', $id)
                          ->first();
      $mail['to'] = $data['email'];
        $mail['subject'] ="Konfirmasi Pesanan";
        
      $update = Transaksi::where('idTransaksi', '=', $id)
                          ->update(
                            ['status' => 2]
                          );
      $response = null;
      if ($update) {
        try{
          Mail::send('test',['data' => $data] ,function($message)use($mail) {
          $message->to($mail['to'], '')
          ->subject($mail['subject']);
          });
        }catch(JWTException $exception){
            $this->serverstatuscode = "0";
            $this->serverstatusdes = $exception->getMessage();
        }
        $response = [
          'sukses' => true,
          'msg' => 'Berhasil Konfrimasi'
        ];
      } else {
        $response = [
          'sukses' => false,
          'msg' => 'Gagal Konfrimasi'
        ];
      }
      return $response;
    }
}
