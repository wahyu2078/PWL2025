<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Level;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Barryvdh\DomPDF\Facade\Pdf;

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

        $level = Level::all(); // ambil data level untuk filter level

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
        $users = User::select('user_id', 'username', 'nama', 'level_id')
            ->with('level');

        // Filter data user berdasarkan level_id
        if ($request->level_id) {
            $users->where('level_id', $request->level_id);
        }

        return DataTables::of($users)
            ->addIndexColumn() // menambahkan kolom index / no urut (default nama kolom: DT_RowIndex)
            ->addColumn('level', function ($user) {
                return $user->level
                    ? $user->level->level_nama . ' (' . $user->level->level_kode . ')'
                    : 'Tidak ada level';
            })
            ->addColumn('aksi', function ($user) {
                $btn = '<button onclick="modalAction(\'' . url('/user/' . $user->user_id . '/show_ajax') . '\')" 
                class="btn btn-info btn-sm">Detail</button> ';
                $btn .= '<button onclick="modalAction(\'' . url('/user/' . $user->user_id . '/edit_ajax') . '\')" 
                class="btn btn-warning btn-sm">Edit</button> ';
                $btn .= '<button onclick="modalAction(\'' . url('/user/' . $user->user_id . '/delete_ajax') . '\')" 
                class="btn btn-danger btn-sm">Hapus</button> ';
                return $btn;
            })
            ->rawColumns(['aksi']) // memberitahu bahwa kolom aksi adalah html
            ->make(true);
    }

    public function show_ajax(string $id)
    {
        $user = User::with('level')->find($id);
        return view('user.show_ajax', compact('user'));
    }


    public function create_ajax()
    {
        $level = Level::select('level_id', 'level_nama')->get();

        return view('user.create_ajax')
            ->with('level', $level);
    }


    public function store_ajax(Request $request)
    {
        // cek apakah request berupa ajax
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'level_id' => 'required|integer',
                'username' => 'required|string|min:3|unique:m_user,username',
                'nama'     => 'required|string|max:100',
                'password' => 'required|min:6',
            ];

            // use Illuminate\Support\Facades\Validator;
            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false, // response status, false: error/gagal, true: berhasil
                    'message' => 'Validasi Gagal',
                    'msgField' => $validator->errors(), // pesan error validasi
                ]);
            }

            User::create($request->all());
            return response()->json([
                'status' => true,
                'message' => 'Data user berhasil disimpan',
            ]);
        }
        return redirect('/');
    }


    public function edit_ajax(string $id)
    {
        $user = User::find($id);
        $level = Level::select('level_id', 'level_nama')->get();

        return view('user.edit_ajax', ['user' => $user, 'level' => $level]);
    }

    public function update_ajax(Request $request, $id)
    {
        // cek apakah request dari ajax
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'level_id' => 'required|integer',
                'username' => 'required|max:20|unique:m_user,username,' . $id . ',user_id',
                'nama'    => 'required|max:100',
                'password' => 'nullable|min:6|max:20'
            ];
            // use Illuminate\Support\Facades\Validator;
            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'status'    => false,    // respon json, true: berhasil, false: gagal 'message' => 'Validasi gagal.',
                    'msgField' => $validator->errors() // menunjukkan field mana yang error
                ]);
            }

            $check = User::find($id);
            if ($check) {
                if (!$request->filled('password')) { // jika password tidak diisi, maka hapus dari request
                    $request->request->remove('password');
                }

                $check->update($request->all());
                return response()->json([
                    'status' => true,
                    'message' => 'Data berhasil diupdate'
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

    public function confirm_ajax(string $id)
    {
        $user = User::find($id);

        return view('user.confirm_ajax', ['user' => $user]);
    }

    public function delete_ajax(Request $request, $id)
    {
        // cek apakah request dari ajax
        if ($request->ajax() || $request->wantsJson()) {
            $user = User::find($id);

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
                $exists = \App\Models\User::where('username', $username)->exists();
                $levelValid = \App\Models\Level::where('level_id', $level_id)->exists();

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
            \App\Models\User::insertOrIgnore($insert);
            return response()->json(['status' => true, 'message' => 'Data level berhasil diimport']);
        } else {
            return response()->json(['status' => false, 'message' => 'Tidak ada data baru yang diimport']);
        }
    }

    public function export_excel()
    {
        // Ambil data user beserta relasi level
        $users = \App\Models\User::with('level')
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
        $users = \App\Models\User::with('level')
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
}
