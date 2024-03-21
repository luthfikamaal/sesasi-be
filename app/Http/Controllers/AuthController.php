<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdatePasswordRequest;
use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserRegisterRequest;
use App\Models\User;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;


class AuthController extends Controller
{
    public function register(UserRegisterRequest $request)
    {
        $data = $request->validated();

        if (User::where('email', $data['email'])->count() == 1) {
            throw new HttpResponseException(response([
                "errors" => [
                    "email" => [
                        "email already registered"
                    ]
                ]
            ], 400));
        }

        $user = new User($data);
        $user->password = Hash::make($data['password']);
        $user->save();

        return response()->json([
            'data' => $user
        ])->setStatusCode(201);
    }

    public function login(UserLoginRequest $request)
    {
        $data = $request->validated();

        $user = User::where('email', $data['email'])->first();
        if (!$user || !Hash::check($data['password'], $user->password)) {
            throw new HttpResponseException(response([
                "errors" => [
                    "message" => [
                        "email or password wrong"
                    ]
                ]
            ], 401));
        }

        $user->token = Str::uuid()->toString();
        $user->save();

        return response()->json([
            'data' => [
                'token' => $user->token
            ]
        ]);
    }

    public function updatePassword(UpdatePasswordRequest $request)
    {
        $data = $request->validated();
        // return $data

        $user = Auth::user();
        if (!Hash::check($data['old_password'], $user->password)) {
            return response()->json([
                'error' => [
                    'messages' => ['Password saat ini tidak sesuai.']
                ]
            ])->setStatusCode(401);
        }

        $userupdated = User::where('email', $user->email)->first();
        $userupdated->password = Hash::make($data['new_password']);
        $userupdated->save();

        return response()->json([
            'data' => [
                'messages' => ['Password berhasil diperbarui.']
            ]
        ])->setStatusCode(201);
    }

    public function resetPassword(Request $request)
    {
        $data = $request->validate([
            'new_password' => ['min:8'],
            'new_password_confirmation' => ['min:8', 'same:new_password']
        ]);

        $user = Auth::user();
        if ($user->reset_password_token != $request->query('token')) {
            return response()->json([
                'error' => [
                    'messages' => ['Token reset password tidak valid.']
                ]
            ])->setStatusCode(403);
        }

        $userupdated = User::where('email', $user->email)->first();
        $userupdated->password = Hash::make($data['new_password']);
        $userupdated->save();

        return response()->json([
            'data' => [
                'messages' => ['Password berhasil diperbarui.']
            ]
        ])->setStatusCode(201);
    }
}
