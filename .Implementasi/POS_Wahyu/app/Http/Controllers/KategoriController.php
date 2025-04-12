<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Models\Kategori;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Barryvdh\DomPDF\Facade\Pdf;

class KategoriController extends Controller
{
    // Menampilkan halaman daftar kategori
    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Daftar Kategori',
            'list'  => ['Home', 'Kategori']
        ];

        $page = (object) [
            'title' => 'Daftar kategori yang terdaftar dalam sistem'
        ];

        $activeMenu = 'kategori';

        // GANTI nama variabel dari $kategori ke $kategori_kode
        $kategori_kode = \App\Models\Kategori::select('kategori_kode')->distinct()->get();

        return view('kategori.index', compact('breadcrumb', 'page', 'activeMenu', 'kategori_kode'));
    }

    // Menampilkan data kategori ke DataTables
    public function list(Request $request)
    {
        $kategori = Kategori::select('kategori_id', 'kategori_kode', 'kategori_nama');

        // Filter berdasarkan kode kategori jika disediakan
        if ($request->filter_kode) {
            $kategori->where('kategori_kode', $request->filter_kode);
        }

        return DataTables::of($kategori)
            ->addIndexColumn() // Menambahkan kolom nomor urut (DT_RowIndex)
            ->addColumn('aksi', function ($kategori) {
                $btn  = '<button onclick="modalAction(\'' . url('/kategori/' . $kategori->kategori_id . '/show_ajax') . '\')" class="btn btn-info btn-sm">Detail</button> ';
                $btn .= '<button onclick="modalAction(\'' . url('/kategori/' . $kategori->kategori_id . '/edit_ajax') . '\')" class="btn btn-warning btn-sm">Edit</button> ';
                $btn .= '<button class="btn btn-danger btn-sm btn-delete-kategori" data-url="' . url('/kategori/' . $kategori->kategori_id . '/delete_ajax') . '">Hapus</button>';
                return $btn;
            })
            ->rawColumns(['aksi']) // Memberitahu DataTables bahwa kolom aksi berisi HTML
            ->make(true);
    }

    public function create_ajax()
    {
        return view('kategori.create_ajax');
    }

    public function store_ajax(Request $request)
    {
        // cek apakah request berupa ajax
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'kategori_kode' => 'required|string|max:10|unique:m_kategori,kategori_kode',
                'kategori_nama' => 'required|string|max:100',
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

            Kategori::create($request->all());
            return response()->json([
                'status' => true,
                'message' => 'Data kategori berhasil disimpan',
            ]);
        }
        return redirect('/');
    }

    public function show_ajax($id)
    {
        $kategori = \App\Models\Kategori::find($id);
        return view('kategori.show_ajax', compact('kategori'));
    }

    public function edit_ajax(string $id)
    {
        $kategori = Kategori::find($id);
        return view('kategori.edit_ajax', ['kategori' => $kategori]);
    }

    public function update_ajax(Request $request, string $id)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'kategori_kode' => 'required|string|max:10',
                'kategori_nama' => 'required|string|max:100',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi Gagal',
                    'msgField' => $validator->errors(),
                ]);
            }
            $check = Kategori::find($id);
            if ($check) {
                $check->update($request->all());
                return response()->json([
                    'status' => true,
                    'message' => 'Data kategori berhasil diubah',
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Data kategori tidak ditemukan',
                ]);
            }
        }
        return redirect('/');
    }

    public function confirm_ajax(string $id)
    {
        $kategori = Kategori::find($id);
        return view('kategori.confirm_ajax', ['kategori' => $kategori]);
    }

    public function delete_ajax(Request $request, string $id)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $kategori = Kategori::find($id);

            if ($kategori) {
                $kategori->delete();
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
        return view('kategori.import');
    }

    public function import_ajax(Request $request)
    {
        $request->validate([
            'file_kategori' => 'required|mimes:xlsx|max:1024'
        ]);

        $file = $request->file('file_kategori');
        $reader = IOFactory::createReader('Xlsx');
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($file->getRealPath());
        $sheet = $spreadsheet->getActiveSheet();
        $data = $sheet->toArray(null, false, true, true); // Kolom: A = kode, B = nama

        $insert = [];

        foreach ($data as $i => $row) {
            if ($i == 1) continue; // Skip header

            if (!empty($row['A']) && !empty($row['B'])) {
                $exists = Kategori::where('kategori_kode', $row['A'])->exists();
                if (!$exists) {
                    $insert[] = [
                        'kategori_kode' => $row['A'],
                        'kategori_nama' => $row['B'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }
        }

        if (count($insert) > 0) {
            Kategori::insertOrIgnore($insert);
            return response()->json([
                'status' => true,
                'message' => 'Data kategori berhasil diimport'
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Tidak ada data baru yang diimport'
            ]);
        }
    }
    public function export_excel()
    {
        // Ambil semua data kategori
        $kategori = \App\Models\Kategori::select('kategori_kode', 'kategori_nama')
            ->orderBy('kategori_kode')
            ->get();

        // Buat spreadsheet baru
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Kategori');

        // Header kolom
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Kode Kategori');
        $sheet->setCellValue('C1', 'Nama Kategori');
        $sheet->getStyle('A1:C1')->getFont()->setBold(true);

        // Isi data dari baris ke-2
        $no = 1;
        $baris = 2;
        foreach ($kategori as $item) {
            $sheet->setCellValue('A' . $baris, $no++);
            $sheet->setCellValue('B' . $baris, $item->kategori_kode);
            $sheet->setCellValue('C' . $baris, $item->kategori_nama);
            $baris++;
        }

        // Atur lebar kolom otomatis
        foreach (range('A', 'C') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        // Siapkan writer dan nama file
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $filename = 'Data_Kategori_' . date('Ymd_His') . '.xlsx';

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
        set_time_limit(60);

        // Ambil semua data kategori
        $kategori = \App\Models\Kategori::select('kategori_kode', 'kategori_nama')
            ->orderBy('kategori_kode')
            ->get();

        // Generate PDF dari view
        $pdf = Pdf::loadView('kategori.export_pdf', ['kategori' => $kategori]);
        $pdf->setPaper('a4', 'portrait');
        $pdf->setOption('isRemoteEnabled', true); // jika ada logo/gambar

        return $pdf->stream('Data_Kategori_' . date('Ymd_His') . '.pdf');
    }
}
