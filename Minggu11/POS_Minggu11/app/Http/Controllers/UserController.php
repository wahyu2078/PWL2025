<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserModel;
use App\Models\LevelModel;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;


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


    // Ambil data user dalam bentuk json untuk datatables public function list(Request $request)
    public function list(Request $request)
    {
        $users = UserModel::select('user_id', 'username', 'nama', 'level_id', 'foto')
            ->with('level');

        if ($request->level_id) {
            $users->where('level_id', $request->level_id);
        }

        return DataTables::of($users)
            ->addIndexColumn()
            ->addColumn('foto', function ($user) {
                $img = $user->foto && file_exists(public_path('uploads/user/' . $user->foto))
                    ? asset('uploads/user/' . $user->foto)
                    : asset('images/default.png'); // path fallback
                return '<img src="' . $img . '" width="40" height="40" class="rounded-circle">';
            })
            ->addColumn('level', function ($user) {
                return $user->level
                    ? $user->level->level_nama . ' (' . $user->level->level_kode . ')'
                    : 'Tidak ada level';
            })
            ->addColumn('aksi', function ($user) {
                $btn = '<button onclick="modalAction(\'' . url('/user/' . $user->user_id . '/show_ajax') . '\')" class="btn btn-info btn-sm">Detail</button> ';
                $btn .= '<button onclick="modalAction(\'' . url('/user/' . $user->user_id . '/edit_ajax') . '\')" class="btn btn-warning btn-sm">Edit</button> ';
                $btn .= '<button onclick="modalAction(\'' . url('/user/' . $user->user_id . '/delete_ajax') . '\')" class="btn btn-danger btn-sm">Hapus</button> ';
                return $btn;
            })
            ->rawColumns(['foto', 'aksi'])
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
            'username' => 'required|string|min:3|unique:m_user,username',
            'nama'     => 'required|string|max:100',
            'password' => 'required|min:5',
            'level_id' => 'required|integer',
            'user_foto' => 'nullable|image|mimes:jpeg,png,jpg|max:1024'
        ]);

        $fotoName = null;
        if ($request->hasFile('user_foto')) {
            $fotoName = Str::uuid() . '.' . $request->file('user_foto')->getClientOriginalExtension();
            $request->file('user_foto')->storeAs('public/uploads/user', $fotoName);
        }

        UserModel::create([
            'username' => $request->username,
            'nama'     => $request->nama,
            'password' => bcrypt($request->password),
            'level_id' => $request->level_id,
            'user_foto' => $fotoName
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
            'username' => 'required|string|min:3|unique:m_user,username,' . $id . ',user_id',
            'nama'     => 'required|string|max:100',
            'password' => 'nullable|min:5',
            'level_id' => 'required|integer',
            'user_foto' => 'nullable|image|mimes:jpeg,png,jpg|max:1024'
        ]);

        $user = UserModel::find($id);
        if (!$user) {
            return redirect('/user')->with('error', 'User tidak ditemukan');
        }

        $fotoName = $user->user_foto;

        if ($request->hasFile('user_foto')) {
            // Hapus foto lama
            if ($fotoName && Storage::exists('public/uploads/user/' . $fotoName)) {
                Storage::delete('public/uploads/user/' . $fotoName);
            }

            $fotoName = Str::uuid() . '.' . $request->file('user_foto')->getClientOriginalExtension();
            $request->file('user_foto')->storeAs('public/uploads/user', $fotoName);
        }

        $user->update([
            'username' => $request->username,
            'nama'     => $request->nama,
            'password' => $request->filled('password') ? bcrypt($request->password) : $user->password,
            'level_id' => $request->level_id,
            'user_foto' => $fotoName
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

    public function create_ajax()
    {
        $level = LevelModel::select('level_id', 'level_nama')->get();
        return view('user.create_ajax')->with('level', $level);
    }



    public function store_ajax(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'level_id' => 'required|integer',
                'username' => 'required|string|min:3|unique:m_user,username',
                'nama'     => 'required|string|max:100',
                'password' => 'required|min:6',
                'foto'     => 'nullable|image|mimes:jpg,jpeg,png|max:1024',
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return response()->json([
                    'status'   => false,
                    'message'  => 'Validasi Gagal',
                    'msgField' => $validator->errors(),
                ]);
            }

            $data = $request->only(['username', 'nama', 'password', 'level_id']);
            $data['password'] = bcrypt($data['password']);

            // Upload foto jika ada
            if ($request->hasFile('foto')) {
                $foto = $request->file('foto');
                $namaFile = uniqid() . '.' . $foto->getClientOriginalExtension();
                $foto->move(public_path('uploads/user'), $namaFile);
                $data['foto'] = $namaFile;
            }

            UserModel::create($data);

            return response()->json([
                'status' => true,
                'message' => 'Data user berhasil disimpan',
            ]);
        }

        return redirect('/');
    }



    public function edit_ajax($id)
    {
        $user = UserModel::find($id);
        $level = LevelModel::select('level_id', 'level_nama')->get();
        return view('user.edit_ajax', compact('user', 'level'));
    }


    public function update_ajax(Request $request, $id)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'level_id' => 'required|integer',
                'username' => 'required|max:20|unique:m_user,username,' . $id . ',user_id',
                'nama'     => 'required|max:100',
                'password' => 'nullable|min:6|max:20',
                'foto'     => 'nullable|image|mimes:jpg,jpeg,png|max:1024',
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return response()->json([
                    'status'    => false,
                    'msgField'  => $validator->errors(),
                ]);
            }

            $user = UserModel::find($id);
            if ($user) {
                $user->username = $request->username;
                $user->nama     = $request->nama;
                $user->level_id = $request->level_id;

                if ($request->filled('password')) {
                    $user->password = bcrypt($request->password);
                }

                if ($request->hasFile('foto')) {
                    // Hapus foto lama jika ada
                    if ($user->foto && file_exists(public_path('uploads/user/' . $user->foto))) {
                        unlink(public_path('uploads/user/' . $user->foto));
                    }

                    $foto = $request->file('foto');
                    $namaFile = uniqid() . '.' . $foto->getClientOriginalExtension();
                    $foto->move(public_path('uploads/user'), $namaFile);
                    $user->foto = $namaFile;
                }

                $user->save();

                return response()->json([
                    'status' => true,
                    'message' => 'Data berhasil diupdate'
                ]);
            }

            return response()->json([
                'status' => false,
                'message' => 'Data tidak ditemukan'
            ]);
        }

        return redirect('/');
    }


    public function confirm_ajax(string $id)
    {
        $user = UserModel::find($id);

        return view('user.confirm_ajax', ['user' => $user]);
    }

    public function delete_ajax(Request $request, $id)
    {
        // cek apakah request dari ajax
        if ($request->ajax() || $request->wantsJson()) {
            $user = UserModel::find($id);

            if ($user) {
                $user->delete();

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

    public function import()
    {
        return view('user.import');
    }

    public function import_ajax(Request $request)
    {
        $request->validate([
            'file_user' => 'required|mimes:xlsx|max:1024'
        ]);

        $file = $request->file('file_user');
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xlsx');
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($file->getRealPath());
        $sheet = $spreadsheet->getActiveSheet();
        $data = $sheet->toArray(null, false, true, true); // Kolom huruf: A, B, C, D

        $insert = [];

        foreach ($data as $i => $row) {
            if ($i == 1) continue; // Skip baris header

            $username = trim($row['A'] ?? '');
            $nama     = trim($row['B'] ?? '');
            $password = trim($row['C'] ?? '');
            $level_id = trim($row['D'] ?? '');

            if ($username !== '' && $nama !== '' && $password !== '' && is_numeric($level_id)) {
                $exists = \App\Models\UserModel::where('username', $username)->exists();
                $levelValid = \App\Models\LevelModel::where('level_id', $level_id)->exists();

                if (!$exists && $levelValid) {
                    $insert[] = [
                        'username'   => $username,
                        'nama'       => $nama,
                        'password'   => bcrypt($password),
                        'level_id'   => $level_id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }
        }

        if (count($insert) > 0) {
            \App\Models\UserModel::insertOrIgnore($insert);
            return response()->json(['status' => true, 'message' => 'Data level berhasil diimport']);
        } else {
            return response()->json(['status' => false, 'message' => 'Tidak ada data baru yang diimport']);
        }
    }

    public function export_excel()
    {
        // Ambil data user beserta relasi level
        $users = \App\Models\UserModel::with('level')
            ->select('username', 'nama', 'level_id')
            ->orderBy('level_id')
            ->get();

        // Buat spreadsheet baru
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data User');

        // Header kolom
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Username');
        $sheet->setCellValue('C1', 'Nama');
        $sheet->setCellValue('D1', 'Level');
        $sheet->getStyle('A1:D1')->getFont()->setBold(true);

        // Isi data dari baris ke-2
        $no = 1;
        $baris = 2;
        foreach ($users as $user) {
            $sheet->setCellValue('A' . $baris, $no++);
            $sheet->setCellValue('B' . $baris, $user->username);
            $sheet->setCellValue('C' . $baris, $user->nama);
            $sheet->setCellValue('D' . $baris, $user->level->level_nama ?? '-');
            $baris++;
        }

        // Atur lebar kolom otomatis
        foreach (range('A', 'D') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        // Output Excel ke browser
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $filename = 'Data_User_' . date('Ymd_His') . '.xlsx';

        // Header response
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        header('Cache-Control: cache, must-revalidate');
        header('Pragma: public');

        $writer->save('php://output');
        exit;
    }

    public function export_pdf()
    {
        // Tambah batas waktu eksekusi (jika diperlukan)
        set_time_limit(60); // aman karena hanya beberapa data

        // Ambil maksimal 10 data user beserta level
        $users = \App\Models\UserModel::with('level')
            ->select('username', 'nama', 'level_id')
            ->orderBy('level_id')
            ->orderBy('username')
            ->limit(10)
            ->get();

        // Generate PDF dari view
        $pdf = Pdf::loadView('user.export_pdf', ['users' => $users]);
        $pdf->setPaper('a4', 'portrait');
        $pdf->setOption('isRemoteEnabled', true); // jika ada logo/gambar

        return $pdf->stream('Data_User_' . date('Ymd_His') . '.pdf');
    }

    public function profile()
    {
        $user = Auth::user();
        $activeMenu = 'profile'; // Tambahkan ini
    
        return view('user.profile', compact('user', 'activeMenu'));
    }

    public function updateProfile(Request $request)
    {
        /** @var UserModel $user */
        $user = Auth::user();

        // Validasi input
        $request->validate([
            'nama' => 'required|string|max:100',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
        ]);

        // Update nama
        $user->nama = $request->nama;

        // Cek & upload file foto jika ada
        if ($request->hasFile('foto')) {
            $foto = $request->file('foto');
            $filename = 'user_' . time() . '.' . $foto->getClientOriginalExtension();
            $foto->move(public_path('uploads/user'), $filename);
            $user->foto = $filename;
        }

        // Simpan data
        $user->save();

        return redirect('/profile')->with('success', 'Profil berhasil diperbarui.');
    }
}
