<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserModel;
use Illuminate\Support\Facades\Hash;


class UserController extends Controller
{
    public function index()
    {
        $user = UserModel::firstOrNew(['username' => 'manager33']);

        // Mengisi atribut yang diperlukan
        $user->nama = 'Manager Tiga Tiga';
        $user->password = Hash::make('12345');
        $user->level_id = 2;

        // Simpan ke database
        $user->save();

        return view('user', ['data' => $user]);
    }
}
