<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Kategori;

class KategoriController extends Controller
{
    public function index()
    {
        $kategori = Kategori::all();
        return view('kategori.index', compact('kategori'));
    }
    
    public function store(Request $request)
    {
        Kategori::create(['nama_kategori' => $request->nama_kategori]);
        return redirect('/kategori')->with('success', 'Kategori berhasil ditambahkan');
    }

}

