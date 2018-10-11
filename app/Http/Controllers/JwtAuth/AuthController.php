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

        if(!$data) {
            return response()->json([
                'type' => 'error',
                'message' => 'Email not found!'
            ], 400);
        }
        $user = $data[0];

        if(Hash::check($request->password, $user->password)) {
            unset($user->password);
            $token = $this->genJWT($user);
            $this->deleteTokenData($user);
            $this->storeTokenData($token, $user, time()+60*60);
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

    //METHOD FOR LOGGING OUT
    public function logout(Request $request) {
        $key = env('SECRET_KEY');
        $token = $request->bearerToken();

        try {
            $decoded = (array) JWT::decode($token, $key, array('HS256'));
            $user_id = $decoded['id'];
            // $token_data = DB::select("SELECT *
            //  FROM tokens
            //  WHERE user_id = '{$user_id}'");
            DB::delete("DELETE FROM tokens WHERE user_id='{$user_id}'");
            return response()->json([
                'type' => 'success',
                'message' => 'Succesfully logged out!'
            ], 200); 
             
        } catch (\Exception $e) {
            return response()->json([
                'type' => 'error',
                'data' => ['errors' => [$e->getMessage()]]
            ], 401);
        }


        return $token;
    }

    //METHOD FOR GENERATING JWT
    private function genJWT($user) {
        $key = env('SECRET_KEY');
        $payload = array(
          "id" => $user->id,
          "time" => time()
        );

        return JWT::encode($payload, $key);
    }
}
