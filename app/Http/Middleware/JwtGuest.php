<?php

namespace App\Http\Middleware;
use Illuminate\Support\Facades\DB;


use Closure;
use \Firebase\JWT\JWT;

class JwtGuest
{
    
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $key = env('SECRET_KEY');
        $token = $request->bearerToken();
        // return response()->json([
        //     'message' => $token
        // ], 200);
        if(!$token) {
            return $next($request);
        } else {
            try {
                $decoded = (array) JWT::decode($token, $key, array('HS256'));
                $user_id = $decoded['id'];
                $user_data = DB::select("SELECT user_id FROM tokens WHERE user_id = '{$user_id}'");
                if(!$user_data) {
                    return $next($request);
                } else {
                    return response()->json([
                        'type' => 'error',
                        'data' => ['errors' => ['You are already logged in!']]
                    ], 403);
                }
            } catch (\Exception $e) {
                return response()->json([
                    'message' => $e->getMessage()
                ], 200);
            }
        }
    }
}
