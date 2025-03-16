<?php

namespace App\Http\Controllers;

use App\Models\KategoriModel;
use Illuminate\Http\Request;
use App\DataTables\KategoriDataTable;

class KategoriController extends Controller
{
    public function index(KategoriDataTable $dataTable)
    {
        return $dataTable->render('kategori.index');
    }

    public function create()
    {
        return view('kategori.create');
    }

    public function edit($id)
    {
        // Mencari data kategori berdasarkan ID, jika tidak ditemukan akan menampilkan error 404
        $kategori = KategoriModel::findOrFail($id);

        // Menampilkan halaman edit dengan data kategori yang ditemukan
        return view('kategori.edit', compact('kategori'));
    }

    public function update(Request $request, $id)
    {
        $kategori = KategoriModel::where('kategori_id', $id)->firstOrFail();

        $kategori->update([
            'kategori_kode' => $request->kodeKategori,
            'kategori_nama' => $request->namaKategori
        ]);

        return redirect()->route('kategori.index')->with('success', 'Kategori berhasil diperbarui');
    }





    // Hapus kategori berdasarkan ID
    public function destroy($id)
    {
        $kategori = KategoriModel::findOrFail($id); // Cari kategori
        $kategori->delete(); // Hapus kategori

        return redirect()->route('kategori.index')
            ->with('success', 'Kategori berhasil dihapus'); // Redirect dengan pesan sukses
    }




    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'kodeKategori' => 'required|string|max:10',
            'namaKategori' => 'required|string|max:255',
        ]);

        // Simpan ke database
        KategoriModel::create([
            'kategori_kode' => $request->kodeKategori,
            'kategori_nama' => $request->namaKategori, // Perbaikan sintaks
        ]);

        return redirect('/kategori')->with('success', 'Kategori berhasil ditambahkan!');
    }
}
