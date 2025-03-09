<?php

namespace App\Http\Controllers;

use App\Models\UserModel;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        // Ambil semua data dari tabel m_user
        $user = UserModel::all();

        // Kirim data ke view user.blade.php
        return view('user', ['data' => $user]);
    }
}
