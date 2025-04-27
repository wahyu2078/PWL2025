<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kategori;

class KategoriController extends Controller
{
    public function index()
    {
        return Kategori::all();
    }

    public function store(Request $request)
    {
        $user = Kategori::create($request->all());
        return response()->json($user, 201);
    }

    public function show($id)
    {
        return Kategori::find($id);
    }

    public function update(Request $request, $id)
    {
        $user = Kategori::find($id);
        $user->update($request->all());
        return response()->json($user);
    }

    public function destroy($id)
    {
        $user = Kategori::find($id);
        $user->delete();
        return response()->json([
            'success' => true,
            'message' => 'Data terhapus',
        ]);
    }
}
