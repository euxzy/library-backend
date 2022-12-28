<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $user = User::query()
            ->where('email', $request->input('email'))
            ->first();

        /**
         * Check user
         */
        if ($user == null) {
            return response()->json([
                'status' => false,
                'message' => 'Email Not Found!',
                'data' => null
            ]);
        }

        /**
         * Check password
         */
        if (!Hash::check($request->input('password'), $user->password)) {
            return response()->json([
                'status' => false,
                'message' => 'Wrong password!',
                'data' => null
            ]);
        }

        /**
         * Create user token
         */
        $token = $user->createToken('auth_token');
        return response()->json([
            'status' => true,
            'message' => '',
            'data' => [
                'auth' => [
                    'token' => $token->plainTextToken,
                    'token_type' => 'Bearer'
                ],
                'user' => $user
            ]
        ]);
    }

    public function getUser(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'status' => true,
            'message' => '',
            'data' => $user
        ]);
    }
}
