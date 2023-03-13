<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $fields = $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|unique:users,email',
            'password' => 'required|string'
        ]);

        $user = User::create([
            'name' => $fields['name'],
            'email' => $fields['email'],
            'password' => bcrypt($fields['password']),
            'phone' => $request->phone,
        ]);

        $token = $user->createToken('myapptoken')->plainTextToken;

        $response = [
            'success' => true,
            'user' => $user,
            'token' => $token
        ];

        return response($response, 201);
    }

    public function login(Request $request)
    {
        $fields = $request->validate([
            'email' => 'required|string',
            'password' => 'required|string'
        ]);

        // Check email
        $user = User::where('email', $fields['email'])->first();

        // Check password
        if (!$user || !Hash::check($fields['password'], $user->password)) {
            return response([
                'success' => false,
                'message' => 'Bad creds'
            ], 401);
        }

        $token = $user->createToken('myapptoken')->plainTextToken;

        $response = [
            'success' => true,
            'user' => $user,
            'token' => $token
        ];

        return response($response, 200);
    }

    public function logout()
    {
        auth()->user()->tokens()->delete();

        return [
            'success' => true,
            'message' => 'Logged out',
        ];
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string',
        ]);

        if (Hash::check($request->current_password, $request->user()->password)) {
            $user = User::find($request->user()->id);
            $user->password = bcrypt($request->new_password);
            $user->update();
            $response = [
                'success' => true,
                'user' => $request->user(),
            ];

            return response($response, 201);
        } else {
            return response([
                'success' => false,
                'message' => 'Bad creds'
            ], 401);
        }
    }
}
