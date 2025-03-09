<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserModel;
use Illuminate\Support\Facades\Hash;


class UserController extends Controller
{
    public function index()
    {
        $user = UserModel::all(); // Mengambil semua data user dari database
        return view('user', ['data' => $user]); // Mengirim data ke tampilan (view)
    }

    public function tambah()
    {
        return view('user_tambah'); // Menampilkan halaman form tambah user
    }

    public function tambah_simpan(Request $request)
    {
        UserModel::create([
            'username' => $request->username,
            'nama' => $request->nama,
            'password' => Hash::make($request->password), // Perbaikan tanda kutip
            'level_id' => $request->level_id
        ]);

        return redirect('/user'); // Redirect ke halaman daftar user setelah menyimpan data
    }

    public function ubah($id)
    {
        $user = UserModel::find($id);
        return view('user_ubah', ['data' => $user]);
    }

    public function ubah_simpan($id, Request $request)
    {
        // Cari user berdasarkan ID
        $user = UserModel::findOrFail($id);

        // Update data user
        $user->username = $request->username;
        $user->nama = $request->nama;
        $user->password = Hash::make($request->password);
        $user->level_id = $request->level_id;

        // Simpan perubahan
        $user->save();

        // Redirect ke halaman user
        return redirect('/user');
    }

    public function hapus($id)
    {
        $user = UserModel::find($id);
        $user->delete();

        return redirect('/user');
    }
}
