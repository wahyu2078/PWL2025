<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LevelModel;

class LevelController extends Controller
{
    // Menampilkan semua data level
    public function index()
    {
        return LevelModel::all();
    }

    // Menyimpan data level baru
    public function store(Request $request)
    {
        $level = LevelModel::create($request->all());
        return response()->json($level, 201);
    }

    // Menampilkan detail satu data level
    public function show($id)
    {
        $level = LevelModel::find($id);
        return response()->json($level);
    }

    // Mengupdate data level
    public function update(Request $request, $id)
    {
        $level = LevelModel::find($id);
        $level->update($request->all());
        return response()->json($level);
    }

    // Menghapus data level
    public function destroy($id)
    {
        $level = LevelModel::find($id);
        $level->delete();
        return response()->json([
            'success' => true,
            'message' => 'Data terhapus',
        ]);
    }
}
