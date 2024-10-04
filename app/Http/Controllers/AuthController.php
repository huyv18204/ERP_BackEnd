<?php

namespace App\Http\Controllers;

use http\Client\Curl\User;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'refresh']]);
    }

    public function login()
    {
        $credentials = request(['email', 'password']);

        if (!$token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $config = [
            'sub' => auth()->user()->id,
            'random' => rand() . time(),
            'exp' => time() + config("jwt.refresh.ttl")
        ];
        $refreshToken = JWTAuth::getJWTProvider()->encode($config);
        return $this->respondWithToken($token, $refreshToken);
    }

    public function me()
    {
        try {
            return response()->json(auth()->user());
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'UnAuthorization'
            ], 401);
        }

    }

    public function logout()
    {
        auth()->logout();
        return response()->json(['message' => 'Successfully logged out']);
    }

    public function refresh()
    {
        try {
            $refreshToken = request()->refresh_token;
            $deCode = JWTAuth::getJWTProvider()->decode($refreshToken);
            $user = \App\Models\User::query()->find($deCode['sub']);
            if (!$user) {
                return response()->json(['error' => " User not found"]);
            }
            \auth()->invalidate();
            $token = \auth()->login($user);
            $config = [
                'sub' => auth()->user()->id,
                'random' => rand() . time(),
                'exp' => time() + config("jwt.refresh.ttl")
            ];
            $refreshToken = JWTAuth::getJWTProvider()->encode($config);
            return $this->respondWithToken($token, $refreshToken);
        } catch (
        \Exception $e
        ) {
            return response()->json('error', "Refresh Token Invalid", 500);
        }

    }

    /**
     * Get the token array structure.
     *
     * @param string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token, $refreshToken)
    {
        return response()->json([
            'access_token' => $token,
            'refresh_token' => $refreshToken,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }
}
