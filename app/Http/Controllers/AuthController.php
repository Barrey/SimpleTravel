<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    /**
     * Provide token for authenticated user
     */
    public function authenticate(Request $request): object
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => 'required'
        ]);

        if (!Auth::attempt($credentials)) {
            return response([
                'message' => 'invalid credentials'
            ], 401);
        }

        $token = $request->user()->createToken($request->email);

        return response()->json([
            'user' => Auth::user(),
            'token' => $token->plainTextToken
        ]);
    }

    /**
     * Remove token
     */
    public function logout(): object
    {
        $request = app(Request::class);

        if(method_exists($request->user()->currentAccessToken(), 'delete')) {
            $request->user()->currentAccessToken()->delete();
        } else {
            auth()->guard('web')->logout();
        }

        return response()->json([
            'message' => 'Logout succed'
        ]);
    }
}
