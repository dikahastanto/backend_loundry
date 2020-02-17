<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
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

    public function register (Request $request) {

        $response;
        $email = $request->email;
        if ($this->emailExists($email, 0, 0)) {
            $response = [
                'sukses' => false,
                'msg' => 'E-Mail Telah Terdaftar',
            ];
        } else {
            $pas = Hash::make($request->password);
            $input = [
                'email' => $email,
                'namaLengkap' => $request->namaLengkap,
                'alamat' => $request->alamat,
                'noTelp' => $request->noTelp,
                'password' => Hash::make($request->password)
            ];
            $reqister = User::create($input);
            if ($reqister) {
                $response = [
                    'sukses' => true,
                    'msg' => 'Berhasil Registrasi',
                ];
            } else {
                $response = [
                    'sukses' => true,
                    'msg' => 'Berhasil Registrasi',
                ];
            }
        }
        return $response;
    }

    public function login(Request $request) {
        $email = $request->email;
        $password = $request->password;

        $response = null;
        $user = User::where('email', $email)->first();

        if ($user) {
            if (Hash::check($password, $user->password)) {
                $apiToken = base64_encode(Str::random(40));
                $user->update([
                    'token' => $apiToken
                ]);
                $response = [
                    'sukses' => true,
                    'msg' => 'Berhasil Login',
                    'user' => $user,
                    'token' => $apiToken
                ];
            } else {
                $response = [
                    'sukses' => false,
                    'msg' => 'Password Salah'
                ];
            }
        } else {
            $response = [
                'sukses' => false,
                'msg' => 'E-Mail Tidak Terdaftar'
            ];
        }
        return $response;
    }

    public function search (Request $request) {
        $keyword = $request->keyword;
        $user = User::where([
            ['namaLengkap', 'LIKE', '%' . $keyword . '%'],
            ['level', '=', 1]
        ])->get();
        return $user;
    }

    public function getData ($id) {
        $user = User::where('id', '=',$id)->first();
        return $user;
    }

    public function changePassword (Request $request, $id) {
        $oldPassword = $request->oldPassword;
        $newPassword = $request->newPassword;
        
        $response;
        $user = User::where('id', $id)->first();
        if (Hash::check($oldPassword, $user->password)) {
            if ($oldPassword == $newPassword) {
                $response = [
                    'sukses' => true,
                    'msg' => 'Tidak Ada Perubahan Data'
                ];
            } else {
                $update = User::where('id', $id)
                                ->update(['password' => Hash::make($newPassword)]);
                if ($update) {
                    $response = [
                        'sukses' => true,
                        'msg' => 'Password Telah Dirubah'
                    ];
                } else {
                    $response = [
                        'sukses' => false,
                        'msg' => 'Terjadi Kesalahan'
                    ];
                }
            }
        } else {
            $response = [
                'sukses' => false,
                'msg' => 'Password Lama Salah'
            ];
        }
        return $response;
    }

    public function updateProfilePelanggan (Request $request, $id) {
        $response;
        if ($this->emailExists($request->email, $id, 1)) {
            $response = [
                'sukses' => false,
                'msg' => 'E-Mail Sudah Digunakan'
            ];
        } else {
            $update = User::where('id', $id)->update($request->all());

            if ($update) {
                $response = [
                    'sukses' => true,
                    'msg' => 'Berhasil Mengubah Data'
                ];
            } else {
                $response = [
                    'sukses' => false,
                    'msg' => 'Gagal Merubah Data'
                ];
            }
        }
        return $response;

    }

    public function emailExists ($email, $id, $type) {
        $query;
        if ($type == 1) {
            $query = [
                ['email', '=', $email],
                ['id', '<>', $id]
            ];
        } else {
            $query = [
                ['email', '=', $email]
            ];
        }
        $cek = User::where($query)->first();
        if ($cek) {
            return true;
        } else {
            return false;
        }
    }

    //
}
