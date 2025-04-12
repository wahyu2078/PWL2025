<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Barryvdh\DomPDF\Facade\Pdf;

class SupplierController extends Controller
{
    //  Menampilkan halaman index supplier
    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Daftar Supplier',
            'list'  => ['Home', 'Supplier']
        ];

        $page = (object) [
            'title' => 'Daftar supplier yang terdaftar dalam sistem'
        ];

        $activeMenu = 'supplier';

        //  Ambil data supplier unik buat filter
        $suppliers = \App\Models\Supplier::select('supplier_nama')->distinct()->get();

        return view('supplier.index', compact('breadcrumb', 'page', 'activeMenu', 'suppliers'));
    }


    //  Ambil data supplier untuk DataTables
    public function list(Request $request)
    {
        $suppliers = Supplier::select('supplier_id', 'supplier_id', 'supplier_nama', 'supplier_alamat');

        return DataTables::of($suppliers)
            // menambahkan kolom index / no urut (default nama kolom: DT_RowIndex)
            ->addIndexColumn()
            ->addColumn('aksi', function ($supplier) { // menambahkan kolom aksi
                // $btn = '<a href="' . url('/supplier/' . $supplier->supplier_id) . '" class="btn btn-info btn-sm">Detail</a> ';
                // $btn .= '<a href="' . url('/supplier/' . $supplier->supplier_id . '/edit') . '" class="btn btn-warning btn-sm">Edit</a> ';
                // $btn .= '<form class="d-inline-block" method="POST" action="' .
                //     url('/supplier/' . $supplier->supplier_id) . '">'
                //     . csrf_field() . method_field('DELETE') .
                //     '<button type="submit" class="btn btn-danger btn-sm" onclick="return confirm(\'Apakah Anda yakin menghapus data ini?\');">Hapus</button></form>';
                $btn = '<button onclick="modalAction(\'' . url('/supplier/' . $supplier->supplier_id .
                    '/show_ajax') . '\')" class="btn btn-info btn-sm">Detail</button> ';
                $btn .= '<button onclick="modalAction(\'' . url('/supplier/' . $supplier->supplier_id .
                    '/edit_ajax') . '\')" class="btn btn-warning btn-sm">Edit</button> ';
                $btn .= '<button onclick="modalAction(\'' . url('/supplier/' . $supplier->supplier_id .
                    '/delete_ajax') . '\')" class="btn btn-danger btn-sm">Hapus</button> ';
                return $btn;
            })
            ->rawColumns(['aksi']) // memberitahu bahwa kolom aksi adalah html
            ->make(true);
    }

    public function create_ajax()
    {
        return view('supplier.create_ajax');
    }

    public function show_ajax($id)
    {
        $supplier = Supplier::find($id);
        return view('supplier.show_ajax', compact('supplier'));
    }


    public function store_ajax(Request $request)
    {
        // cek apakah request berupa ajax
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'supplier_id'   => 'required|string|max:10|unique:m_supplier,supplier_id',
                'supplier_nama'   => 'required|string|max:100',
                'supplier_alamat' => 'required|string|max:255',
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

            Supplier::create($request->all());
            return response()->json([
                'status' => true,
                'message' => 'Data supplier berhasil disimpan',
            ]);
        }
        return redirect('/');
    }

    public function edit_ajax(string $id)
    {
        $supplier = Supplier::find($id);
        return view('supplier.edit_ajax', ['supplier' => $supplier]);
    }

    public function update_ajax(Request $request, string $id)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'supplier_id'   => 'required|string|max:10',
                'supplier_nama'   => 'required|string|max:100',
                'supplier_alamat' => 'required|string|max:255',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi Gagal',
                    'msgField' => $validator->errors(),
                ]);
            }
            $check = Supplier::find($id);
            if ($check) {
                $check->update($request->all());
                return response()->json([
                    'status' => true,
                    'message' => 'Data supplier berhasil diubah',
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Data supplier tidak ditemukan',
                ]);
            }
        }
        return redirect('/');
    }

    public function confirm_ajax(string $id)
    {
        $supplier = Supplier::find($id);
        return view('supplier.confirm_ajax', ['supplier' => $supplier]);
    }

    public function delete_ajax(Request $request, string $id)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $supplier = Supplier::find($id);

            if ($supplier) {
                $supplier->delete();
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
        return view('supplier.import'); // tampilkan modal form
    }

    public function import_ajax(Request $request)
    {
        // Validasi file upload
        $request->validate([
            'file_supplier' => 'required|mimes:xlsx|max:1024'
        ]);

        // Baca file Excel
        $file = $request->file('file_supplier');
        $reader = IOFactory::createReader('Xlsx');
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($file->getRealPath());
        $sheet = $spreadsheet->getActiveSheet();
        $data = $sheet->toArray(null, false, true, true); // mode A, B, ...

        $insert = [];

        foreach ($data as $i => $row) {
            if ($i == 1) continue; // Lewati header

            $nama   = trim($row['A'] ?? '');
            $alamat = trim($row['B'] ?? '');

            if ($nama !== '') {
                $exists = Supplier::where('supplier_nama', $nama)->exists();

                if (!$exists) {
                    $insert[] = [
                        'supplier_nama'   => $nama,
                        'supplier_alamat' => $alamat,
                        'created_at'      => now(),
                        'updated_at'      => now(),
                    ];
                }
            }
        }

        // Simpan ke DB
        if (count($insert) > 0) {
            Supplier::insertOrIgnore($insert);
            return response()->json([
                'status' => true,
                'message' => 'Data supplier berhasil diimport.'
            ]);
        }

        return response()->json([
            'status' => false,
            'message' => 'Tidak ada data baru yang diimport.'
        ]);
    }

    public function export_excel()
    {
        // Ambil semua data supplier
        $suppliers = \App\Models\Supplier::select('supplier_nama', 'supplier_alamat')
            ->orderBy('supplier_nama')
            ->get();

        // Buat spreadsheet baru
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Supplier');

        // Header kolom
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Nama Supplier');
        $sheet->setCellValue('C1', 'Alamat Supplier');
        $sheet->getStyle('A1:C1')->getFont()->setBold(true);

        // Isi data dari baris ke-2
        $no = 1;
        $baris = 2;
        foreach ($suppliers as $item) {
            $sheet->setCellValue('A' . $baris, $no++);
            $sheet->setCellValue('B' . $baris, $item->supplier_nama);
            $sheet->setCellValue('C' . $baris, $item->supplier_alamat ?? '-');
            $baris++;
        }

        // Atur lebar kolom otomatis
        foreach (range('A', 'C') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        // Siapkan writer dan nama file
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $filename = 'Data_Supplier_' . date('Ymd_His') . '.xlsx';

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

        // Ambil semua data supplier
        $suppliers = \App\Models\Supplier::select('supplier_nama', 'supplier_alamat')
            ->orderBy('supplier_nama')
            ->get();

        // Generate PDF dari view
        $pdf = Pdf::loadView('supplier.export_pdf', ['suppliers' => $suppliers]);
        $pdf->setPaper('a4', 'portrait');
        $pdf->setOption('isRemoteEnabled', true); // jika ada logo/gambar

        return $pdf->stream('Data_Supplier_' . date('Ymd_His') . '.pdf');
    }
}
