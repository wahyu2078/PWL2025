<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class LogoutController extends Controller
{
    public function __invoke(Request $request)
    {
        try {
            // remove token
            $removeToken = JWTAuth::invalidate(JWTAuth::getToken());

            if ($removeToken) {
                // return response JSON
                return response()->json([
                    'success' => true,
                    'message' => 'Logout Berhasil!',
                ]);
            }
        } catch (JWTException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Logout gagal!',
            ], 500);
        }
    }
}
