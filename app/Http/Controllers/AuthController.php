<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        $credentials = $request->only(['email', 'password']);

        if (!$token = Auth::guard('api')->attempt($credentials)) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Invalid credentials'
            ], 401);
        }

        return $this->respondWithToken($token);
    }

    public function logout()
    {
        Auth::guard('api')->logout();

        return response()->json([
            'success' => true,
            'data' => null,
            'message' => 'Successfully logged out'
        ]);
    }

    public function me()
    {
        $user = Auth::guard('api')->user();

        return response()->json([
            'success' => true,
            'data' => $user,
            'message' => 'User retrieved successfully'
        ]);
    }

    public function refresh()
    {
        $token = Auth::guard('api')->refresh();

        return $this->respondWithToken($token);
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'success' => true,
            'data' => [
                'access_token' => $token,
                'token_type' => 'bearer',
                'expires_in' => Auth::guard('api')->factory()->getTTL() * 60
            ],
            'message' => 'Login successful'
        ]);
    }
}
