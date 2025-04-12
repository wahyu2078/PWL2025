<?php

namespace App\Http\Controllers;

use App\Models\Level;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Barryvdh\DomPDF\Facade\Pdf;

class LevelController extends Controller
{
    // Menampilkan halaman index level
    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Daftar Level',
            'list'  => ['Home', 'Level']
        ];

        $page = (object) [
            'title' => 'Daftar level yang terdaftar dalam sistem'
        ];

        $activeMenu = 'level';

        //  Ambil distinct level_kode buat dropdown filter
        $level_kode = Level::select('level_kode')->distinct()->get();

        //  Kirim $level_kode ke view
        return view('level.index', compact('breadcrumb', 'page', 'activeMenu', 'level_kode'));
    }

    // Mengambil data level untuk DataTables
    public function list(Request $request)
    {
        $levels = Level::select('level_id', 'level_kode', 'level_nama');

        if ($request->filter_level) {
            $levels->where('level_kode', $request->filter_level);
        }

        return DataTables::of($levels)
            ->addIndexColumn()
            ->addColumn('aksi', function ($level) {
                $showUrl   = url('/level/' . $level->level_id . '/show_ajax');
                $editUrl   = url('/level/' . $level->level_id . '/edit_ajax');
                $deleteUrl = url('/level/' . $level->level_id . '/delete_ajax');

                $btn  = '<button onclick="modalAction(\'' . $showUrl . '\')" class="btn btn-info btn-sm">Detail</button> ';
                $btn .= '<button onclick="modalAction(\'' . $editUrl . '\')" class="btn btn-warning btn-sm">Edit</button> ';
                $btn .= '<button class="btn btn-danger btn-sm btn-delete-level" data-url="' . $deleteUrl . '">Hapus</button>';

                return $btn;
            })
            ->rawColumns(['aksi']) // penting agar tombol ditampilkan sebagai HTML
            ->make(true);
    }


    public function create_ajax()
    {
        return view('level.create_ajax');
    }
    public function store_ajax(Request $request)
    {
        if ($request->ajax()) {
            $validator = Validator::make($request->all(), [
                'level_kode' => 'required|string|max:5|unique:m_level,level_kode',
                'level_nama' => 'required|string|max:100',
            ]);

            if ($validator->fails()) {
                return response()->json(['status' => false, 'message' => 'Validasi Gagal', 'msgField' => $validator->errors()]);
            }

            Level::create($request->all());
            return response()->json(['status' => true, 'message' => 'Data level berhasil disimpan']);
        }
        return redirect('/');
    }

    public function show_ajax($id)
    {
        $level = Level::find($id);
        return view('level.show_ajax', compact('level'));
    }


    public function edit_ajax(string $id)
    {
        return view('level.edit_ajax', ['level' => Level::find($id)]);
    }

    public function update_ajax(Request $request, string $id)
    {
        if ($request->ajax()) {
            $validator = Validator::make($request->all(), [
                'level_kode' => 'required|string|max:5',
                'level_nama' => 'required|string|max:100',
            ]);

            if ($validator->fails()) {
                return response()->json(['status' => false, 'message' => 'Validasi Gagal', 'msgField' => $validator->errors()]);
            }

            $level = Level::find($id);
            if ($level) {
                $level->update($request->all());
                return response()->json(['status' => true, 'message' => 'Data level berhasil diubah']);
            }
            return response()->json(['status' => false, 'message' => 'Data level tidak ditemukan']);
        }
        return redirect('/');
    }

    public function confirm_ajax(string $id)
    {
        return view('level.confirm_ajax', ['level' => Level::find($id)]);
    }

    // Menghapus data level
    public function delete_ajax(Request $request, string $id)
    {
        if ($request->ajax()) {
            $level = Level::find($id);
            if ($level) {
                $level->delete();
                return response()->json(['status' => true, 'message' => 'Data berhasil dihapus']);
            }
            return response()->json(['status' => false, 'message' => 'Data tidak ditemukan']);
        }
        return redirect('/');
    }

    public function import()
    {
        return view('level.import');
    }

    public function import_ajax(Request $request)
    {
        $request->validate([
            'file_level' => 'required|mimes:xlsx|max:1024'
        ]);

        $file = $request->file('file_level');
        $reader = IOFactory::createReader('Xlsx');
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($file->getRealPath());
        $sheet = $spreadsheet->getActiveSheet();
        $data = $sheet->toArray(null, false, true, true);

        $insert = [];

        foreach ($data as $i => $row) {
            if ($i == 1) continue; // Skip header
            if (!empty($row['A']) && !empty($row['B'])) {
                $exists = \App\Models\Level::where('level_kode', $row['A'])->exists();
                if (!$exists) {
                    $insert[] = [
                        'level_kode' => $row['A'],
                        'level_nama' => $row['B'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }
        }

        if (count($insert) > 0) {
            \App\Models\Level::insertOrIgnore($insert);
            return response()->json(['status' => true, 'message' => 'Data level berhasil diimport']);
        } else {
            return response()->json(['status' => false, 'message' => 'Tidak ada data baru yang diimport']);
        }
    }

    public function export_excel()
    {
        // Ambil semua data level
        $levels = \App\Models\Level::select('level_kode', 'level_nama')
            ->orderBy('level_kode')
            ->get();

        // Buat spreadsheet baru
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Level');

        // Header kolom
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Kode Level');
        $sheet->setCellValue('C1', 'Nama Level');
        $sheet->getStyle('A1:C1')->getFont()->setBold(true);

        // Isi data dari baris ke-2
        $no = 1;
        $baris = 2;
        foreach ($levels as $level) {
            $sheet->setCellValue('A' . $baris, $no++);
            $sheet->setCellValue('B' . $baris, $level->level_kode);
            $sheet->setCellValue('C' . $baris, $level->level_nama);
            $baris++;
        }

        // Atur lebar kolom otomatis
        foreach (range('A', 'C') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        // Siapkan writer dan nama file
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $filename = 'Data_Level_' . date('Ymd_His') . '.xlsx';

        // Header response
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        header('Cache-Control: cache, must-revalidate');
        header('Pragma: public');

        // Output file ke browser
        $writer->save('php://output');
        exit;
    }

    public function export_pdf()
    {
        // Tambah batas waktu eksekusi jika dibutuhkan
        set_time_limit(60); // aman karena datanya sedikit

        // Ambil semua data level (atau limit jika mau dibatasi)
        $levels = \App\Models\Level::select('level_kode', 'level_nama')
            ->orderBy('level_kode')
            ->get();

        // Generate PDF dari view
        $pdf = Pdf::loadView('level.export_pdf', ['levels' => $levels]);
        $pdf->setPaper('a4', 'portrait');
        $pdf->setOption('isRemoteEnabled', true); // jika ada gambar/logo

        return $pdf->stream('Data_Level_' . date('Ymd_His') . '.pdf');
    }
}
