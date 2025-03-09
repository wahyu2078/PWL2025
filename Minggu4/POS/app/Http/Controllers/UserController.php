<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserModel;
use Illuminate\Support\Facades\Hash;


class UserController extends Controller
{
    public function index()
    {
        // Membuat user baru
        $user = UserModel::create([
            'username' => 'manager11',
            'nama' => 'Manager11',
            'password' => Hash::make('12345'),
            'level_id' => 2,
        ]);

        // Mengubah username
        $user->username = 'manager12';

        // Simpan perubahan
        $user->save();

        // Cek apakah ada perubahan setelah `save()`
        $user->wasChanged(); // true (karena ada perubahan)
        $user->wasChanged('username'); // true (username berubah)
        $user->wasChanged(['username', 'level_id']); // true (karena username berubah, meskipun level_id tidak berubah)
        $user->wasChanged('nama'); // false (nama tidak berubah)

        // Hentikan eksekusi dan tampilkan hasil
        dd($user->wasChanged(['nama', 'username'])); // true (karena username berubah)
    }
}
