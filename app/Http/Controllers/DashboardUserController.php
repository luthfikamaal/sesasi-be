<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DashboardUserController extends Controller
{
    public function index()
    {
        $users = User::all();

        return response()->json([
            'data' => [
                'users' => $users
            ]
        ])->setStatusCode(200);
    }

    public function addVerificator($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json([
                'error' => [
                    'messages' => [
                        'Not found'
                    ]
                ]
            ]);
        }

        $user->role = 'verificator';
        $user->save();

        return response()->json([
            'data' => [
                'user' => $user,
                'messages' => [
                    'User role has been updated.'
                ]
            ]
        ]);
    }

    public function resetPassword($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json([
                'error' => [
                    'messages' => [
                        'Not found'
                    ]
                ]
            ]);
        }

        $user->reset_password_token = Str::uuid()->toString();
        $user->save();

        return response()->json([
            'data' => [
                'link' => "http://localhost:8000/api/users/reset-password?token=" . $user->reset_password_token
            ]
        ]);
    }
}
