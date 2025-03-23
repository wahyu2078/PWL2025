<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use App\Models\Kategori;

class KategoriController extends Controller
{
    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Daftar Kategori',
            'list'  => ['Home', 'Kategori']
        ];
    
        $page = (object) [
            'title' => 'Daftar kategori yang terdaftar dalam sistem'
        ];
    
        $activeMenu = 'kategori';
    
        return view('kategori.index', compact('breadcrumb', 'page', 'activeMenu'));
    }
    
    public function list(Request $request)
    {
        $kategoris = Kategori::select('kategori_id', 'kategori_kode', 'kategori_nama');
    
        return DataTables::of($kategoris)
            ->addIndexColumn()
            ->addColumn('aksi', function($kategori) {
                return '<a href="'.url('/kategori/'.$kategori->kategori_id).'" class="btn btn-info btn-sm">Detail</a>
                        <a href="'.url('/kategori/'.$kategori->kategori_id.'/edit').'" class="btn btn-warning btn-sm">Edit</a>';
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }
}
