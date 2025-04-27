<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Penjualan;
use App\Models\DetailPenjualan;
use Illuminate\Support\Facades\Validator;

class PenjualanController extends Controller
{
    // POST - Buat Transaksi
    public function store(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'pembeli' => 'required',
            'penjualan_kode' => 'required',
            'penjualan_tanggal' => 'required|date',
            'detail' => 'required|array',
            'detail.*.barang_id' => 'required|exists:m_barang,barang_id',
            'detail.*.harga' => 'required|numeric',
            'detail.*.jumlah' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Simpan Penjualan
        $penjualan = Penjualan::create([
            'user_id' => $request->user_id,
            'pembeli' => $request->pembeli,
            'penjualan_kode' => $request->penjualan_kode,
            'penjualan_tanggal' => $request->penjualan_tanggal,
        ]);

        // Simpan Detail Penjualan
        foreach ($request->detail as $item) {
            DetailPenjualan::create([
                'penjualan_id' => $penjualan->penjualan_id,
                'barang_id' => $item['barang_id'],
                'harga' => $item['harga'],
                'jumlah' => $item['jumlah'],
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Transaksi berhasil disimpan',
            'data' => $penjualan
        ], 201);
    }

    // GET - List Semua Penjualan
    public function index()
    {
        $penjualan = Penjualan::with(['details.barang'])->get();

        return response()->json([
            'success' => true,
            'data' => $penjualan
        ]);
    }

    // GET - Detail Penjualan by ID
    public function show($id)
    {
        $penjualan = Penjualan::with(['details.barang'])->find($id);

        if (!$penjualan) {
            return response()->json([
                'success' => false,
                'message' => 'Transaksi tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $penjualan
        ]);
    }
}
