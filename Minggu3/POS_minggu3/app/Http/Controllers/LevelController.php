<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LevelController extends Controller
{
    public function index()
    {
        // **INSERT DATA (jika ingin menambah data)**
        // DB::insert('INSERT INTO m_level (level_kode, level_nama, created_at) VALUES (?, ?, ?)', ['CUS', 'Pelanggan', now()]);
        // return "Insert data baru berhasil";

        // **UPDATE DATA**
        // $row = DB::update('UPDATE m_level SET level_nama = ? WHERE level_kode = ?', ['Customer', 'CUS']);
        // return "Update data berhasil. Jumlah data yang diupdate: " . $row . " baris";

        // **DELETE DATA**
        // $row = DB::delete('DELETE FROM m_level WHERE level_kode = ?', ['CUS']);
        // return "Delete data berhasil. Jumlah data yang dihapus: " . $row . " baris";

        // **SELECT DATA**
        $data = DB::select('SELECT * FROM m_level');
        return view('level', ['data' => $data]);
    }
}
