<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Models\Kategori;

class KategoriController extends Controller
{
    // Menampilkan halaman daftar kategori
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
    
        //  Ambil daftar kode kategori buat dropdown filter
        $kategori = \App\Models\Kategori::select('kategori_kode')->distinct()->get();
    
        //  Kirim data kategori ke view
        return view('kategori.index', compact('breadcrumb', 'page', 'activeMenu', 'kategori'));
    }
    

    // Menampilkan data kategori ke DataTables
    public function list(Request $request)
{
    $kategori = Kategori::query();

    //  Cek filter kode kategori
    if ($request->filter_kode) {
        $kategori->where('kategori_kode', $request->filter_kode);
    }

    return DataTables::of($kategori)
        ->addIndexColumn()
        ->addColumn('aksi', function ($kategori) {
            return '<a href="' . url('/kategori/' . $kategori->kategori_id) . '" class="btn btn-info btn-sm">Detail</a>
                    <a href="' . url('/kategori/' . $kategori->kategori_id . '/edit') . '" class="btn btn-warning btn-sm">Edit</a>
                    <form class="d-inline-block" method="POST" action="' . url('/kategori/' . $kategori->kategori_id) . '">'
                    . csrf_field() . method_field('DELETE') .
                    '<button type="submit" class="btn btn-danger btn-sm" onclick="return confirm(\'Yakin hapus kategori ini?\');">Hapus</button></form>';
        })
        ->rawColumns(['aksi'])
        ->make(true);
}


    // Halaman form tambah kategori
    public function create()
    {
        $breadcrumb = (object) [
            'title' => 'Tambah Kategori',
            'list'  => ['Home', 'Kategori', 'Tambah']
        ];

        $page = (object) [
            'title' => 'Tambah kategori baru'
        ];

        $activeMenu = 'kategori';

        return view('kategori.create', compact('breadcrumb', 'page', 'activeMenu'));
    }

    // Menyimpan data kategori baru
    public function store(Request $request)
    {
        $request->validate([
            'kategori_kode' => 'required|string|min:3|unique:m_kategori,kategori_kode',
            'kategori_nama' => 'required|string|max:100'
        ]);

        Kategori::create($request->all());

        return redirect('/kategori')->with('success', 'Data kategori berhasil ditambahkan!');
    }

    // Menampilkan detail kategori
    public function show($id)
    {
        $kategori = Kategori::findOrFail($id);
    
        $breadcrumb = (object) [
            'title' => 'Detail Kategori',
            'list'  => ['Home', 'Kategori', 'Detail']
        ];
    
        // 🔥 Tambahin $page biar nggak error di view
        $page = (object) [
            'title' => 'Detail Kategori'
        ];
    
        $activeMenu = 'kategori';
    
        return view('kategori.show', compact('breadcrumb', 'page', 'kategori', 'activeMenu'));
    }
    

    // Halaman form edit kategori
    public function edit($id)
    {
        $kategori = Kategori::findOrFail($id);
    
        $breadcrumb = (object) [
            'title' => 'Edit Kategori',
            'list'  => ['Home', 'Kategori', 'Edit']
        ];
    
        // 🛠️ Ini yang kurang! Tambahin $page biar nggak error
        $page = (object) [
            'title' => 'Edit kategori'
        ];
    
        $activeMenu = 'kategori';
    
        return view('kategori.edit', compact('breadcrumb', 'page', 'kategori', 'activeMenu'));
    }

    // Menyimpan perubahan data kategori
    public function update(Request $request, $id)
    {
        $request->validate([
            'kategori_kode' => 'required|string|min:3|unique:m_kategori,kategori_kode,' . $id . ',kategori_id',
            'kategori_nama' => 'required|string|max:100'
        ]);

        Kategori::find($id)->update($request->all());

        return redirect('/kategori')->with('success', 'Data kategori berhasil diubah!');
    }

    // Menghapus kategori
    public function destroy($id)
    {
        Kategori::destroy($id);

        return redirect('/kategori')->with('success', 'Data kategori berhasil dihapus!');
    }
}
