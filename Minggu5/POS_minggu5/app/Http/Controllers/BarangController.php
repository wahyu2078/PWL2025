<?php

namespace App\Http\Controllers;

use App\Models\Barang;  // Import model Barang
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class BarangController extends Controller
{
    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Daftar Barang',
            'list'  => ['Home', 'Barang']
        ];

        $page = (object) [
            'title' => 'Daftar barang yang terdaftar dalam sistem'
        ];

        $activeMenu = 'barang';

        return view('barang.index', compact('breadcrumb', 'page', 'activeMenu'));
    }

    public function list(Request $request)
    {
        // Mengambil data barang
        $barangs = Barang::select('barang_id', 'barang_kode', 'barang_nama', 'harga_beli', 'harga_jual');

        // Mengembalikan data dalam format DataTables
        return DataTables::of($barangs)
            ->addIndexColumn()
            ->addColumn('aksi', function($barang) {
                // Menambahkan tombol aksi Detail dan Edit
                return '<a href="'.url('/barang/'.$barang->barang_id).'" class="btn btn-info btn-sm">Detail</a>
                        <a href="'.url('/barang/'.$barang->barang_id.'/edit').'" class="btn btn-warning btn-sm">Edit</a>';
            })
            ->rawColumns(['aksi'])  // Menandai kolom aksi sebagai kolom yang berisi HTML
            ->make(true);
    }
}
