<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Produk;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ProdukController extends Controller
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

          if ($file->move('img/produk', $name)) {
              $data = [
                'nama' => $request->nama,
                'harga' => $request->harga,
                'isi' => $request->isi,
                'gambar' => $name,
                'idUser' => $request->idUser
              ];
              $insert = Produk::create($data);
              if ($insert) {
                $response = [
                  'sukses' => true,
                  'msg' => 'Berhasil Input Produk'
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
          'msg' => 'Mohon Isi Data Dengan Benar'
        ];
      }
      return $response;
    }

    public function getByIdUser ($id) {
      $produk = Produk::where('idUser', '=', $id)->get();
      return response()->json($produk);
    }
    public function getAll () {
      $produk = Produk::join('users', 'tb_produk.idUser', '=', 'users.id')->get();
      return response()->json($produk);
    }
}
