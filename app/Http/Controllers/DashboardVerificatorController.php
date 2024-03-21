<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddVerificatorRequest;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DashboardVerificatorController extends Controller
{
    public function store(AddVerificatorRequest $request)
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
        $user->role = 'verificator';
        $user->save();

        return response()->json([
            'data' => [
                'user' => $user,
                'messages' => ['Verificator created successfully.']
            ]
        ])->setStatusCode(201);
    }

    public function verifyUser($id)
    {
        $user = User::where('id', $id)->first();

        if (!$user) {
            return response()->json([
                "errors" => [
                    'messages' => ['User not found.']
                ]
            ])->setStatusCode(404);
        }

        $user->is_verified = 1;
        $user->save();
        return response()->json([
            'data' => [
                'user' => $user,
                'messages' => ['User verified successfully.']
            ]
        ])->setStatusCode(201);
    }

    public function userToVerificator($id)
    {
        $user = User::where('id', $id)->first();

        if (!$user) {
            return response()->json([
                "errors" => [
                    'messages' => ['User tidak ditemukan.']
                ]
            ])->setStatusCode(404);
        }

        $user->role = 'verificator';
        $user->save();
        return response()->json([
            'data' => [
                'user' => $user,
                'messages' => ['User berhasil diubah menjadi verificator.']
            ]
        ])->setStatusCode(201);
    }
}
