<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class SupplierController extends Controller
{
    //  Menampilkan halaman index supplier
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
    
        //  Ambil data supplier unik buat filter
        $suppliers = \App\Models\Supplier::select('supplier_nama')->distinct()->get();
    
        return view('supplier.index', compact('breadcrumb', 'page', 'activeMenu', 'suppliers'));
    }
    

    //  Ambil data supplier untuk DataTables
    public function list(Request $request)
    {
        $suppliers = \App\Models\Supplier::select('supplier_id', 'supplier_nama', 'supplier_alamat');
    
        //  Filter nama supplier kalau ada input filter
        if ($request->has('supplier_nama') && $request->supplier_nama != '') {
            $suppliers->where('supplier_nama', $request->supplier_nama);
        }
    
        return DataTables::of($suppliers)
            ->addIndexColumn()
            ->addColumn('aksi', function ($supplier) {
                $btn = '<a href="' . url('/supplier/' . $supplier->supplier_id) . '" class="btn btn-info btn-sm">Detail</a> ';
                $btn .= '<a href="' . url('/supplier/' . $supplier->supplier_id . '/edit') . '" class="btn btn-warning btn-sm">Edit</a> ';
                $btn .= '<form class="d-inline-block" method="POST" action="' .
                    url('/supplier/' . $supplier->supplier_id) . '">' .
                    csrf_field() . method_field('DELETE') .
                    '<button type="submit" class="btn btn-danger btn-sm" onclick="return confirm(\'Yakin hapus supplier ini?\');">Hapus</button></form>';
                return $btn;
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }
    

    //  Halaman form tambah supplier
    public function create()
    {
        $breadcrumb = (object) [
            'title' => 'Tambah Supplier',
            'list'  => ['Home', 'Supplier', 'Tambah']
        ];

        $page = (object) [
            'title' => 'Tambah supplier baru'
        ];

        $activeMenu = 'supplier';

        return view('supplier.create', compact('breadcrumb', 'page', 'activeMenu'));
    }

    //  Simpan data supplier baru
    public function store(Request $request)
    {
        $request->validate([
            'supplier_nama'    => 'required|string|max:100',
            'supplier_alamat'  => 'required|string',
        ]);

        Supplier::create([
            'supplier_nama'    => $request->supplier_nama,
            'supplier_alamat'  => $request->supplier_alamat,
        ]);

        return redirect('/supplier')->with('success', 'Data supplier berhasil disimpan');
    }

    //  Tampilkan detail supplier
    public function show($id)
    {
        $supplier = Supplier::findOrFail($id);

        $breadcrumb = (object) [
            'title' => 'Detail Supplier',
            'list'  => ['Home', 'Supplier', 'Detail']
        ];

        $page = (object) [
            'title' => 'Detail supplier'
        ];

        $activeMenu = 'supplier';

        return view('supplier.show', compact('breadcrumb', 'page', 'supplier', 'activeMenu'));
    }

    //  Halaman edit supplier
    public function edit($id)
    {
        $supplier = Supplier::findOrFail($id);

        $breadcrumb = (object) [
            'title' => 'Edit Supplier',
            'list'  => ['Home', 'Supplier', 'Edit']
        ];

        $page = (object) [
            'title' => 'Edit supplier'
        ];

        $activeMenu = 'supplier';

        return view('supplier.edit', compact('breadcrumb', 'page', 'supplier', 'activeMenu'));
    }

    //  Simpan perubahan data supplier
    public function update(Request $request, $id)
    {
        $request->validate([
            'supplier_nama'    => 'required|string|max:100',
            'supplier_alamat'  => 'required|string',
        ]);

        Supplier::findOrFail($id)->update([
            'supplier_nama'    => $request->supplier_nama,
            'supplier_alamat'  => $request->supplier_alamat,
        ]);

        return redirect('/supplier')->with('success', 'Data supplier berhasil diubah');
    }

    //  Hapus data supplier
    public function destroy($id)
    {
        $supplier = Supplier::find($id);
        if (!$supplier) {
            return redirect('/supplier')->with('error', 'Data supplier tidak ditemukan');
        }

        try {
            $supplier->delete();
            return redirect('/supplier')->with('success', 'Data supplier berhasil dihapus');
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect('/supplier')->with('error', 'Data supplier gagal dihapus karena masih terkait dengan data lain');
        }
    }
}
