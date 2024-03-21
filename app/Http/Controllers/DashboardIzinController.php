<?php

namespace App\Http\Controllers;

use App\Models\Izin;
use Illuminate\Http\Request;

class DashboardIzinController extends Controller
{
    public function index()
    {
        return response()->json([
            'data' => [
                'izin' => Izin::all()
            ]
        ]);
    }

    public function actionIzin(Request $request, $id)
    {
        $data = $request->validate([
            'status' => ['required'],
            'komentar' => ['required']
        ]);

        $izin = Izin::with('user')->where('id', $id)->first();
        if (!$izin) {
            return response()->json([
                'error' => [
                    'messages' => ['Something wrong']
                ]
            ])->setStatusCode(500);
        }

        $izin->status = $data['status'];
        $izin->komentar = $data['komentar'];
        $izin->save();

        return response()->json([
            'data' => [
                'messages' => ['Pengajuan izin telah d.'],
                'izin' => $izin
            ]
        ])->setStatusCode(201);
    }
}
