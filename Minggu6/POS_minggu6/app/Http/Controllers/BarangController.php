<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class BarangController extends Controller
{
    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Daftar Barang',
            'list' => ['Home', 'Barang']
        ];

        $page = (object) [
            'title' => 'Daftar barang yang terdaftar dalam sistem'
        ];

        $activeMenu = 'barang';

        $kategori = Kategori::all(); // ambil data kategori untuk filter kategori

        return view('barang.index', ['breadcrumb' => $breadcrumb, 'page' => $page, 'kategori' => $kategori, 'activeMenu' => $activeMenu]);
    }
    public function list(Request $request)
    {
        $barangs = Barang::select('barang_id', 'barang_kode', 'barang_nama', 'harga_beli', 'harga_jual', 'kategori_id')
            ->with('kategori');

        // Filter data barang berdasarkan kategori_id
        if ($request->kategori_id) {
            $barangs->where('kategori_id', $request->kategori_id);
        }
            
        return DataTables::of($barangs)
            // menambahkan kolom index / no urut (default nama kolom: DT_RowIndex)
            ->addIndexColumn()
            ->addColumn('aksi', function ($barang) { // menambahkan kolom aksi
                // $btn = '<a href="' . url('/barang/' . $barang->barang_id) . '" class="btn btn-info btn-sm">Detail</a> ';
                // $btn .= '<a href="' . url('/barang/' . $barang->barang_id . '/edit') . '" class="btn btn-warning btn-sm">Edit</a> ';
                // $btn .= '<form class="d-inline-block" method="POST" action="' .
                //     url('/barang/' . $barang->barang_id) . '">'
                //     . csrf_field() . method_field('DELETE') .
                //     '<button type="submit" class="btn btn-danger btn-sm" onclick="return confirm(\'Apakah Anda yakin menghapus data ini?\');">Hapus</button></form>';
                $btn = '<button onclick="modalAction(\'' . url('/barang/' . $barang->barang_id .
                '/show_ajax') . '\')" class="btn btn-info btn-sm">Detail</button> ';
                $btn .= '<button onclick="modalAction(\'' . url('/barang/' . $barang->barang_id .
                '/edit_ajax') . '\')" class="btn btn-warning btn-sm">Edit</button> ';
                 $btn .= '<button onclick="modalAction(\'' . url('/barang/' . $barang->barang_id .
                '/delete_ajax') . '\')" class="btn btn-danger btn-sm">Hapus</button> ';
                return $btn;
            })
            ->rawColumns(['aksi']) // memberitahu bahwa kolom aksi adalah html
            ->make(true);
    }
    // Menampilkan halaman form tambah barang
    public function create()
    {
        $breadcrumb = (object) [
            'title' => 'Tambah Barang',
            'list' => ['Home', 'Barang', 'Tambah']
        ];

        $page = (object) [
            'title' => 'Tambah barang baru'
        ];

        $kategori = Kategori::all(); // ambil data kategori untuk ditampilkan di form
        $activeMenu = 'barang'; // set menu yang sedang aktif

        return view('barang.create', ['breadcrumb' => $breadcrumb, 'page' => $page, 'kategori' => $kategori, 'activeMenu' => $activeMenu]);
    }
    // Menyimpan data barang baru
    public function store(Request $request)
    {
        $request->validate([
            'barang_kode'   => 'required|string|min:3|unique:m_barang,barang_kode',
            'barang_nama'   => 'required|string|max:100',
            'harga_beli'    => 'required|min:4',
            'harga_jual'    => 'required|min:4',
            'kategori_id'   => 'required|integer'
        ]);

        Barang::create([
            'barang_kode'   => $request->barang_kode,
            'barang_nama'   => $request->barang_nama,
            'harga_beli'    => $request->harga_beli,
            'harga_jual'    => $request->harga_jual,
            'kategori_id'   => $request->kategori_id
        ]);

        return redirect('/barang')->with('success', 'Data barang berhasil disimpan');
    }
    // Menampilkan detail barang
    public function show(string $id)
    {
        $barang = Barang::with('kategori')->find($id);

        $breadcrumb = (object) [
            'title' => 'Detail Barang',
            'list'  => ['Home', 'Barang', 'Detail']
        ];

        $page = (object) [
            'title' => 'Detail barang'
        ];

        $activeMenu = 'barang'; // set menu yang sedang aktif

        return view('barang.show', [
            'breadcrumb'    => $breadcrumb,
            'page'          => $page,
            'barang'        => $barang,
            'activeMenu'    => $activeMenu
        ]);
    }
    // Menampilkan halaman form edit barang
    public function edit(string $id)
    {
        $barang = Barang::find($id);
        $kategori = Kategori::all();

        $breadcrumb = (object) [
            'title' => 'Edit Barang',
            'list'  => ['Home', 'Barang', 'Edit']
        ];

        $page = (object) [
            'title' => 'Edit barang'
        ];

        $activeMenu = 'barang'; // set menu yang sedang aktif

        return view('barang.edit', ['breadcrumb' => $breadcrumb, 'page' => $page, 'barang' => $barang, 'kategori' => $kategori, 'activeMenu' => $activeMenu]);
    }

    // Menyimpan perubahan data barang
    public function update(Request $request, string $id)
    {
        $request->validate([
            'barang_kode' => 'required|string|min:3|unique:m_barang,barang_kode,' . $id . ',barang_id',
            'barang_nama'   => 'required|string|max:100',
            'harga_beli'    => 'required|min:4',
            'harga_jual'    => 'required|min:4',
            'kategori_id'   => 'required|integer'
        ]);

        Barang::find($id)->update([
            'barang_kode'   => $request->barang_kode,
            'barang_nama'   => $request->barang_nama,
            'harga_beli'    => $request->harga_beli,
            'harga_jual'    => $request->harga_jual,
            'kategori_id'   => $request->kategori_id
        ]);

        return redirect('/barang')->with('success', 'Data barang berhasil diubah');
    }
    // Menghapus data barang
    public function destroy(string $id)
    {
        $check = Barang::find($id);
        if (!$check) { 
            // untuk mengecek apakah data barang dengan id yang dimaksud ada atau tidak
            return redirect('/barang')->with('error', 'Data barang tidak ditemukan');
        }

        try {
            Barang::destroy($id); // Hapus data kategori

            return redirect('/barang')->with('success', 'Data barang berhasil dihapus');
        } catch (\Illuminate\Database\QueryException $e) {
            // Jika terjadi error ketika menghapus data, redirect kembali ke halaman dengan membawa pesan error
            return redirect('/barang')->with('error', 'Data barang gagal dihapus karena masih terdapat tabel lain yang terkait dengan data ini');
        }
    }

    public function create_ajax()
    {
        $kategori = Kategori::select('kategori_id', 'kategori_nama')->get();
        return view('barang.create_ajax')
            ->with('kategori', $kategori);
    }

    public function store_ajax(Request $request)
    {
        // cek apakah request berupa ajax
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'barang_kode'   => 'required|string|max:10|unique:m_barang,barang_kode',
                'barang_nama'   => 'required|string|max:100',
                'kategori_id'   => 'required|integer',
                'harga_beli'    => 'required|integer',
                'harga_jual'    => 'required|integer',
            ];
            // use Illuminate\Support\Facades\Validator;
            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false, // response status, false: error/gagal, true: berhasil
                    'message' => 'Validasi Gagal',
                    'msgField' => $validator->errors(), // pesan error validasi
                ]);
            }

            Barang::create($request->all());
            return response()->json([
                'status' => true,
                'message' => 'Data Barang berhasil disimpan',
            ]);
        }
        return redirect('/');
    }

    public function edit_ajax(string $id)
    {
        $barang = Barang::find($id);
        $kategori = Kategori::select('kategori_id', 'kategori_nama')->get();

        return view('barang.edit_ajax', ['kategori' => $kategori ,'barang' => $barang]);
    }

    public function update_ajax(Request $request, string $id)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'barang_kode'   => 'required|max:10|unique:m_barang,barang_kode,' . $id . ',barang_id',
                'barang_nama'   => 'required|max:100',
                'kategori_id'   => 'required|integer',
                'harga_beli'    => 'required|integer',
                'harga_jual'    => 'required|integer',
            ];
            // use Illuminate\Support\Facades\Validator;
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return response()->json([
                    'status' => false, // respon json, true: berhasil, false: gagal
                    'message' => 'Validasi gagal.',
                    'msgField' => $validator->errors(), // menunjukkan field mana yang error
                ]);
            }
            $check = Barang::find($id);
            if ($check) {
                if (!$request->filled('password')) {
                    $request->request->remove('password');
                }
                $check->update($request->all());
                return response()->json([
                    'status' => true,
                    'message' => 'Data berhasil diupdate',
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Data tidak ditemukan',
                ]);
            }
        }
        return redirect('/');
    }

    public function confirm_ajax(string $id)
    {
        $barang = Barang::find($id);
        return view('barang.confirm_ajax', ['barang' => $barang]);
    }

    public function delete_ajax(Request $request, string $id)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $barang = Barang::find($id);

            if ($barang) {
                $barang->delete();
                return response()->json([
                    'status' => true,
                    'message' => 'Data berhasil dihapus'
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Data tidak ditemukan'
                ]);
            }
        }
        return redirect('/');
    }

}