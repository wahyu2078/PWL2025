<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KategoriController extends Controller
{
    public function index()
    {
        /*
        // Insert data baru ke dalam tabel m_kategori
        $data = [
            'kategori_kode' => 'SNK',
            'kategori_nama' => 'Snack/Makanan Ringan',
            'created_at' => now(),
        ];

        DB::table('m_kategori')->insert($data);
        return 'Insert data baru berhasil';
        */

        /*
        // Update kategori_nama berdasarkan kategori_kode
        $row = DB::table('m_kategori')
                ->where('kategori_kode', 'SNK')
                ->update(['kategori_nama' => 'Camilan']);
        return 'Update data berhasil. Jumlah data yang diupdate: ' . $row . ' baris';
        */

        /*
        // Hapus data berdasarkan kategori_kode
        $row = DB::table('m_kategori')->where('kategori_kode', 'SNK')->delete();
        return 'Delete data berhasil. Jumlah data yang dihapus: ' . $row . ' baris';
        */

        // Ambil data dari tabel m_kategori
        $data = DB::table('m_kategori')->get();
        return view('kategori', ['data' => $data]);
    }
}
