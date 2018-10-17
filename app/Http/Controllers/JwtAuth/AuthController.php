<?php

namespace App\Http\Controllers\JwtAuth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use App\Traits\AuthTrait;
use Illuminate\Support\Facades\Validator;
use \Firebase\JWT\JWT;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    use AuthTrait;


    //POST METHOD FOR REGISTRATION
    public function register(Request $request) {
        $validation = Validator::make($request->all(),[ 
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);
        if($validation->fails()) {
            return response()->json([
                'type' => 'error',
                'data' => $validation->errors()
            ], 400);
        }

        $status = $this->storeUser($request->all(), 'User');
        if($status) {
            return response()->json([
                'type' => 'success',
                'message' => 'User succesfully created!'
            ], 200);
        } else {
            return response()->json([
                'type' => 'error',
                'message' => 'Something went wrong! We are working hard to fix it!'
            ]);
        }
    }


    //POST METHOD FOR LOGIN
    public function login(Request $request) {
        $validation = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255|exists:users,email',
            'password' => 'required|string|min:6'
        ]);

        if($validation->fails()) {
            return response()->json([
                'type' => 'error',
                'data' => $validation->errors()
            ], 400);
        }
        $data = $this->findByEmailWithRole($request->email);
        $user = $data[0];

        if(Hash::check($request->password, $user->password)) {
            unset($user->password);
            $token = $this->genJWT($user);
            return response()->json([
                'type' => 'success',
                'message' => 'Sucessfully logged in!',
                'token' => $token,
                'user' => $user
            ], 200);
        }
        return response()->json([
            'type' => 'error',
            'data' => ['errors' => ['Password is incorrect!']]
        ], 400);
    }

    //METHOD FOR GENERATING JWT
    private function genJWT($user) {
        $key = env('SECRET_KEY');
        $payload = array(
          "id" => $user->id,
          "exp" => time() + 60*60
        );

        return JWT::encode($payload, $key);
    }
}
