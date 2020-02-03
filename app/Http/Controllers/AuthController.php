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
        $pas = Hash::make($request->password);
        $input = [
            'email' => $request->email,
            'namaLengkap' => $request->namaLengkap,
            'alamat' => $request->alamat,
            'password' => Hash::make($request->password)
        ];
        $reqister = User::create($input);
        $response = null;
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

    //
}
