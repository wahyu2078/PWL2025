<?php

namespace App\Http\Controllers;

use App\Models\Supplier;  // Import model Supplier
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class SupplierController extends Controller
{
    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Daftar Supplier',
            'list'  => ['Home', 'Supplier']
        ];

        $page = (object) [
            'title' => 'Daftar supplier yang terdaftar dalam sistem'
        ];

        $activeMenu = 'supplier';

        return view('supplier.index', compact('breadcrumb', 'page', 'activeMenu'));
    }

    public function list(Request $request)
    {
        // Mengambil data supplier
        $suppliers = Supplier::select('supplier_id', 'supplier_nama', 'supplier_alamat');

        // Mengembalikan data dalam format DataTables
        return DataTables::of($suppliers)
            ->addIndexColumn()
            ->addColumn('aksi', function($supplier) {
                // Menambahkan tombol aksi Detail dan Edit
                return '<a href="'.url('/supplier/'.$supplier->supplier_id).'" class="btn btn-info btn-sm">Detail</a>
                        <a href="'.url('/supplier/'.$supplier->supplier_id.'/edit').'" class="btn btn-warning btn-sm">Edit</a>';
            })
            ->rawColumns(['aksi'])  // Menandai kolom aksi sebagai kolom yang berisi HTML
            ->make(true);
    }
}
