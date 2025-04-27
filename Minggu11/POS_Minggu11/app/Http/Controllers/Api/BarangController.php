<?php

namespace App\Http\Controllers\Api;

use App\Models\Barang;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class BarangController extends Controller
{
    // POST: Upload Barang
    public function store(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'kategori_id' => 'required',
            'barang_kode' => 'required',
            'barang_nama' => 'required',
            'barang_foto' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'harga_beli' => 'required|numeric',
            'harga_jual' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Simpan file foto
        $fotoPath = $request->file('barang_foto')->store('barang', 'public');

        // Buat barang
        $barang = Barang::create([
            'kategori_id' => $request->kategori_id,
            'barang_kode' => $request->barang_kode,
            'barang_nama' => $request->barang_nama,
            'barang_foto' => basename($fotoPath),
            'harga_beli' => $request->harga_beli,
            'harga_jual' => $request->harga_jual,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Barang berhasil disimpan',
            'data' => $barang
        ], 201);
    }

    // GET: Ambil Semua Barang
    public function index()
    {
        $barang = Barang::with('kategori')->get();
        return response()->json([
            'success' => true,
            'data' => $barang
        ]);
    }

    public function show($id)
    {
        $barang = Barang::find($id);

        if (!$barang) {
            return response()->json([
                'success' => false,
                'message' => 'Barang tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $barang,
        ]);
    }
}
