<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddIzinRequest;
use App\Http\Requests\EditIzinRequest;
use App\Models\Izin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IzinController extends Controller
{
    public function index()
    {
        $data_izin = Izin::where('id_user', Auth::user()->id)->get();
        if (!$data_izin) {
            return response()->json([
                'error' => [
                    'messages' => [
                        'Not found'
                    ]
                ]
            ])->setStatusCode(404);
        }

        return response()->json([
            'data' => [
                'izin' => $data_izin
            ]
        ])->setStatusCode(200);
    }

    public function create(AddIzinRequest $request)
    {
        $data = $request->validated();

        $izin = new Izin($data);
        $izin->id_user = Auth::user()->id;
        $izin->save();

        return response()->json([
            'data' => [
                'messages' => 'Izin telah diajukan',
                'izin' => $izin
            ]
        ])->setStatusCode(201);
    }

    public function show($id)
    {
        $izin = Izin::with('user')->where('id_user', Auth::user()->id)->where('id', $id)->first();
        if (!$izin) {
            return response()->json([
                'error' => [
                    'messages' => ['Not found.']
                ]
            ])->setStatusCode(404);
        }

        return response()->json([
            'data' => $izin
        ])->setStatusCode(200);
    }

    public function update(EditIzinRequest $request, $id)
    {
        $data = $request->validated();


        $izin = Izin::with('user')->where('id_user', Auth::user()->id)->where('id', $id)->first();

        if (!$izin) {
            return response()->json([
                'error' => [
                    'messages' => ['Not found.']
                ]
            ])->setStatusCode(404);
        }

        if ($izin->dibatalkan == '1') {
            return response()->json([
                'error' => [
                    'messages' => ['Pengajuan izin Anda telah dibatalkan. Silakan ajukan perizinan ulang.']
                ]
            ])->setStatusCode(403);
        }

        if ($izin->status == 'Ditolak') {
            return response()->json([
                'error' => [
                    'messages' => ['Pengajuan izin Anda telah ditolak. Silakan ajukan perizinan ulang.']
                ]
            ])->setStatusCode(403);
        }

        $izin->update($data);

        return response()->json([
            'data' => $izin
        ])->setStatusCode(200);
    }

    public function cancel($id)
    {
        $izin = Izin::with('user')->where('id_user', Auth::user()->id)->where('id', $id)->first();
        if (!$izin) {
            return response()->json([
                'error' => [
                    'messages' => ['Not found.']
                ]
            ])->setStatusCode(404);
        }

        if ($izin->dibatalkan == '1') {
            return response()->json([
                'error' => [
                    'messages' => ['Not found.']
                ]
            ])->setStatusCode(403);
        }
        $izin->dibatalkan = '1';
        $izin->save();

        return response()->json([
            'data' => $izin
        ])->setStatusCode(200);
    }

    public function destroy($id)
    {
        $izin = Izin::with('user')->where('id_user', Auth::user()->id)->where('id', $id)->first();
        if (!$izin) {
            return response()->json([
                'error' => [
                    'messages' => ['Something wrong']
                ]
            ])->setStatusCode(500);
        }

        if ($izin->status == 'Diterima') {
            return response()->json([
                'data' => [
                    'messages' => ['Pengajuan tidak dapat dihapus']
                ]
            ])->setStatusCode(403);
        }

        $izin->delete();

        return response()->json([
            'data' => [
                'messages' => ['Pengajuan izin telah dihapus.']
            ]
        ])->setStatusCode(201);
    }
}
