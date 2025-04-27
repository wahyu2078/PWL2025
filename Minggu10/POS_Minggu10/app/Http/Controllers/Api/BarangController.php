<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Barang;

class BarangController extends Controller
{
    public function index()
    {
        return Barang::all();
    }

    public function store(Request $request)
    {
        $user = Barang::create($request->all());
        return response()->json($user, 201);
    }

    public function show($id)
    {
        return Barang::find($id);
    }

    public function update(Request $request, $id)
    {
        $user = Barang::find($id);
        $user->update($request->all());
        return response()->json($user);
    }

    public function destroy($id)
    {
        $user = Barang::find($id);
        $user->delete();
        return response()->json([
            'success' => true,
            'message' => 'Data terhapus',
        ]);
    }
}
