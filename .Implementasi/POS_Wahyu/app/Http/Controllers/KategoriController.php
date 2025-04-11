<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Models\Kategori;
use Illuminate\Support\Facades\Validator;

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

        // GANTI nama variabel dari $kategori ke $kategori_kode
        $kategori_kode = \App\Models\Kategori::select('kategori_kode')->distinct()->get();

        return view('kategori.index', compact('breadcrumb', 'page', 'activeMenu', 'kategori_kode'));
    }

    // Menampilkan data kategori ke DataTables
    public function list(Request $request)
    {
        $kategori = Kategori::select('kategori_id', 'kategori_kode', 'kategori_nama');

        // Filter berdasarkan kode kategori jika disediakan
        if ($request->filter_kode) {
            $kategori->where('kategori_kode', $request->filter_kode);
        }

        return DataTables::of($kategori)
            ->addIndexColumn() // Menambahkan kolom nomor urut (DT_RowIndex)
            ->addColumn('aksi', function ($kategori) {
                $btn  = '<button onclick="modalAction(\'' . url('/kategori/' . $kategori->kategori_id . '/show_ajax') . '\')" class="btn btn-info btn-sm">Detail</button> ';
                $btn .= '<button onclick="modalAction(\'' . url('/kategori/' . $kategori->kategori_id . '/edit_ajax') . '\')" class="btn btn-warning btn-sm">Edit</button> ';
                $btn .= '<button class="btn btn-danger btn-sm btn-delete-kategori" data-url="' . url('/kategori/' . $kategori->kategori_id . '/delete_ajax') . '">Hapus</button>';
                return $btn;
            })
            ->rawColumns(['aksi']) // Memberitahu DataTables bahwa kolom aksi berisi HTML
            ->make(true);
    }

    public function create_ajax()
    {
        return view('kategori.create_ajax');
    }

    public function store_ajax(Request $request)
    {
        // cek apakah request berupa ajax
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'kategori_kode' => 'required|string|max:10|unique:m_kategori,kategori_kode',
                'kategori_nama' => 'required|string|max:100',
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

            Kategori::create($request->all());
            return response()->json([
                'status' => true,
                'message' => 'Data kategori berhasil disimpan',
            ]);
        }
        return redirect('/');
    }

    public function show_ajax($id)
    {
        $kategori = \App\Models\Kategori::find($id);
        return view('kategori.show_ajax', compact('kategori'));
    }

    public function edit_ajax(string $id)
    {
        $kategori = Kategori::find($id);
        return view('kategori.edit_ajax', ['kategori' => $kategori]);
    }

    public function update_ajax(Request $request, string $id)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'kategori_kode' => 'required|string|max:10',
                'kategori_nama' => 'required|string|max:100',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi Gagal',
                    'msgField' => $validator->errors(),
                ]);
            }
            $check = Kategori::find($id);
            if ($check) {
                $check->update($request->all());
                return response()->json([
                    'status' => true,
                    'message' => 'Data kategori berhasil diubah',
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Data kategori tidak ditemukan',
                ]);
            }
        }
        return redirect('/');
    }

    public function confirm_ajax(string $id)
    {
        $kategori = Kategori::find($id);
        return view('kategori.confirm_ajax', ['kategori' => $kategori]);
    }

    public function delete_ajax(Request $request, string $id)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $kategori = Kategori::find($id);

            if ($kategori) {
                $kategori->delete();
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
