<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use App\Models\Level;

class LevelController extends Controller
{
    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Daftar Level',
            'list'  => ['Home', 'Level']
        ];
    
        $page = (object) [
            'title' => 'Daftar level yang terdaftar dalam sistem'
        ];
    
        $activeMenu = 'level';
    
        return view('level.index', compact('breadcrumb', 'page', 'activeMenu'));
    }
    
    public function list(Request $request)
    {
        $levels = Level::select('level_id', 'level_kode', 'level_nama');
    
        return DataTables::of($levels)
            ->addIndexColumn()
            ->addColumn('aksi', function($level) {
                return '<a href="'.url('/level/'.$level->level_id).'" class="btn btn-info btn-sm">Detail</a>
                        <a href="'.url('/level/'.$level->level_id.'/edit').'" class="btn btn-warning btn-sm">Edit</a>';
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }
}
