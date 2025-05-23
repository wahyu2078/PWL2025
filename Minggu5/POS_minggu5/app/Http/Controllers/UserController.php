<?php

namespace App\Http\Controllers; 

use Illuminate\Http\Request;
use App\Models\UserModel;
use App\Models\LevelModel;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    // Menampilkan halaman awal user
    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Daftar User',
            'list' => ['Home', 'User']
        ];
    
        $page = (object) [
            'title' => 'Daftar user yang terdaftar dalam sistem'
        ];
    
        $activeMenu = 'user'; // set menu yang sedang aktif
    
        $level = LevelModel::all(); // ambil data level untuk filter level
    
        return view('user.index', [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'level' => $level,
            'activeMenu' => $activeMenu
        ]);
    }
    

    // Ambil data user dalam bentuk JSON untuk DataTables
    public function list(Request $request)
    {
        $users = UserModel::with('level')->select('user_id', 'username', 'nama', 'level_id');
    
        if ($request->level_id) {
            $users->where('level_id', $request->level_id); // Filter kalau ada level dipilih
        }
    
        return DataTables::of($users)
            ->addIndexColumn()
            ->addColumn('level', function ($user) {
                return $user->level 
                    ? $user->level->level_nama . ' (' . $user->level->level_kode . ')' 
                    : 'Tidak ada level';
            })
            ->addColumn('aksi', function ($user) {
                return '
                    <a href="' . url('/user/' . $user->user_id) . '" class="btn btn-info btn-sm">Detail</a>
                    <a href="' . url('/user/' . $user->user_id . '/edit') . '" class="btn btn-warning btn-sm">Edit</a>
                    <form class="d-inline-block" method="POST" action="' . url('/user/' . $user->user_id) . '">' 
                    . csrf_field() . method_field('DELETE') . 
                    '<button type="submit" class="btn btn-danger btn-sm" onclick="return confirm(\'Yakin ingin menghapus data ini?\');">Hapus</button></form>';
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }
    


    public function create()
    {
        $breadcrumb = (object) [
            'title' => 'Tambah User',
            'list' => ['Home', 'User', 'Tambah']
        ];

        $page = (object) [
            'title' => 'Tambah user baru'
        ];

        $level = LevelModel::all(); // ambil data level untuk ditampilkan di form
        $activeMenu = 'user'; // set menu yang sedang aktif

        return view('user.create', ['breadcrumb' => $breadcrumb, 'page' => $page, 'level' => $level, 'activeMenu' => $activeMenu]);
    }

    public function store(Request $request)
    {
        $request->validate([
            // username harus diisi, berupa string, minimal 3 karakter, dan bernilai unik di tabel m_user kolom username
            'username' => 'required|string|min:3|unique:m_user,username',
            // nama harus diisi, berupa string, dan maksimal 100 karakter
            'nama'     => 'required|string|max:100',
            // password harus diisi dan minimal 5 karakter
            'password' => 'required|min:5',
            // level_id harus diisi dan berupa angka
            'level_id' => 'required|integer'
        ]);

        UserModel::create([
            'username' => $request->username,
            'nama'     => $request->nama,
            'password' => bcrypt($request->password), // password dienkripsi sebelum disimpan
            'level_id' => $request->level_id
        ]);

        return redirect('/user')->with('success', 'Data user berhasil disimpan');
    }   
     // Menampilkan detail user
     public function show(string $id)
     {
         $user = UserModel::with('level')->find($id);
     
         if (!$user) {
             return redirect('/user')->with('error', 'Data user tidak ditemukan');
         }
     
         $breadcrumb = (object) [
             'title' => 'Detail User',
             'list'  => ['Home', 'User', 'Detail']
         ];
     
         $page = (object) [
             'title' => 'Detail user'
         ];
     
         $activeMenu = 'user';
     
         return view('user.show', [
             'breadcrumb' => $breadcrumb,
             'page'       => $page,
             'user'       => $user,
             'activeMenu' => $activeMenu
         ]);
     }
     
     public function edit(string $id)
     {
         $user = UserModel::find($id);
         $level = LevelModel::all();
 
         $breadcrumb = (object) [
             'title' => 'Edit User',
             'list'  => ['Home', 'User', 'Edit']
         ];
 
         $page = (object) [
             'title' => 'Edit user'
         ];
 
         $activeMenu = 'user'; // set menu yang sedang aktif
 
         return view('user.edit', ['breadcrumb' => $breadcrumb, 'page' => $page, 'user' => $user, 'level' => $level, 'activeMenu' => $activeMenu]);
     }
 
     // Menyimpan perubahan data user
     public function update(Request $request, string $id)
     {
         $request->validate([
             // username harus diisi, berupa string, minimal 3 karakter,
             // dan bernilai unik di tabel m_user kolom username kecuali untuk user dengan id yang sedang diedit
             'username' => 'required|string|min:3|unique:m_user,username,' . $id . ',user_id',
             'nama'     => 'required|string|max:100', // nama harus diisi, berupa string, dan maksimal 100 karakter
             'password' => 'nullable|min:5',          // password bisa diisi (minimal 5 karakter) dan bisa tidak diisi
             'level_id' => 'required|integer'         // level_id harus diisi dan berupa angka
         ]);
 
         UserModel::find($id)->update([
             'username' => $request->username,
             'nama'     => $request->nama,
             'password' => $request->password ? bcrypt($request->password) : UserModel::find($id)->password,
             'level_id' => $request->level_id
         ]);
 
         return redirect('/user')->with('success', 'Data user berhasil diubah');
     }
     public function destroy(string $id)
     {
         $check = UserModel::find($id);
         if (!$check) { 
             // untuk mengecek apakah data user dengan id yang dimaksud ada atau tidak
             return redirect('/user')->with('error', 'Data user tidak ditemukan');
         }
 
         try {
             UserModel::destroy($id); // Hapus data level
 
             return redirect('/user')->with('success', 'Data user berhasil dihapus');
         } catch (\Illuminate\Database\QueryException $e) {
             // Jika terjadi error ketika menghapus data, redirect kembali ke halaman dengan membawa pesan error
             return redirect('/user')->with('error', 'Data user gagal dihapus karena masih terdapat tabel lain yang terkait dengan data ini');
         }
     }
}