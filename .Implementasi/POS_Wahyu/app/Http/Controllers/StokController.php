<?php

namespace App\Http\Controllers;

use App\Models\Stok;
use App\Models\Barang;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Barryvdh\DomPDF\Facade\Pdf;
use Yajra\DataTables\Facades\DataTables;

class StokController extends Controller
{
    public function index()
    {
        $breadcrumb = (object)[
            'title' => 'Daftar Stok',
            'list' => ['Home', 'Stok']
        ];

        $page = (object)[
            'title' => 'Daftar stok barang yang terdaftar dalam sistem'
        ];

        $activeMenu = 'stok';

        return view('stok.index', compact('breadcrumb', 'page', 'activeMenu'));
    }

    public function list(Request $request)
    {
        $stok = Stok::with(['barang', 'user'])->select('t_stok.*');
    
        return DataTables::of($stok)
            ->addIndexColumn()
            ->addColumn('barang_nama', fn($stok) => $stok->barang->barang_nama ?? '-')
            ->addColumn('user_nama', fn($stok) => $stok->user->nama ?? '-')
            ->addColumn('aksi', function ($stok) {
                return '
                    <button onclick="modalAction(\'' . url('/stok/' . $stok->stok_id . '/edit_ajax') . '\')" class="btn btn-warning btn-sm"><i class="fa fa-edit"></i></button>
                    <button onclick="hapusData(\'' . url('/stok/' . $stok->stok_id . '/delete_ajax') . '\')" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i></button>
                ';
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }
    
    

    public function create_ajax()
    {
        $barang = Barang::all();
        return view('stok.create_ajax', compact('barang'));
    }

    public function store_ajax(Request $request)
    {
        $request->merge(['user_id' => auth()->user()->user_id]);

        if ($request->ajax()) {
            $rules = [
                'barang_id'     => 'required|exists:m_barang,barang_id',
                'user_id'       => 'required|exists:m_user,user_id',
                'stok_tanggal'  => 'required|date',
                'stok_jumlah'   => 'required|integer',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'msgField' => $validator->errors()
                ]);
            }

            $barang = Barang::find($request->barang_id);
            $barang->update([
                'stok' => $barang->stok + $request->stok_jumlah
            ]);

            Stok::create($request->all());

            return response()->json([
                'status' => true,
                'message' => 'Data stok berhasil disimpan'
            ]);
        }

        return redirect('/');
    }

    public function edit_ajax(string $id)
    {
        $stok = Stok::find($id);
        $barang = Barang::all();

        return view('stok.edit_ajax', compact('stok', 'barang'));
    }

    public function update_ajax(Request $request, $id)
    {
        if ($request->ajax()) {
            $rules = [
                'barang_id'     => 'required|exists:m_barang,barang_id',
                'stok_tanggal'  => 'required|date',
                'stok_jumlah'   => 'required|integer',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'msgField' => $validator->errors()
                ]);
            }

            $stok = Stok::find($id);

            if ($stok) {
                $barang = Barang::find($request->barang_id);
                $barang->update([
                    'stok' => $barang->stok + $stok->stok_jumlah - $request->stok_jumlah
                ]);

                $stok->update($request->all());

                return response()->json([
                    'status' => true,
                    'message' => 'Data stok berhasil diperbarui'
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
        $stok = Stok::find($id);
        return view('stok.confirm_ajax', compact('stok'));
    }

    public function delete_ajax(Request $request, $id)
    {
        if ($request->ajax()) {
            // Cari stok berdasarkan ID
            $stok = Stok::find($id);
    
            if (!$stok) {
                // Jika stok tidak ditemukan, tampilkan error
                return response()->json([
                    'status' => false,
                    'message' => 'Data stok tidak ditemukan'
                ]);
            }
    
            // Cari barang terkait dengan stok yang akan dihapus
            $barang = Barang::find($stok->barang_id);
    
            if (!$barang) {
                // Jika barang terkait tidak ditemukan, tampilkan error
                return response()->json([
                    'status' => false,
                    'message' => 'Barang terkait tidak ditemukan'
                ]);
            }
    
            // Mengurangi jumlah stok barang yang bersangkutan
            $barang->update([
                'stok' => $barang->stok - $stok->stok_jumlah
            ]);
    
            // Menghapus data stok
            $stok->delete();
    
            return response()->json([
                'status' => true,
                'message' => 'Data stok berhasil dihapus'
            ]);
        }
    
        // Jika bukan permintaan AJAX, redirect ke halaman utama
        return redirect('/');
    }
    
    

    public function import()
    {
        return view('stok.import');
    }

    public function import_ajax(Request $request)
    {
        if ($request->ajax()) {
            $rules = [
                'file_stok' => ['required', 'mimes:xlsx', 'max:1024']
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'msgField' => $validator->errors()
                ]);
            }

            $file = $request->file('file_stok');
            $reader = IOFactory::createReader('Xlsx');
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($file->getRealPath());
            $sheet = $spreadsheet->getActiveSheet();
            $data = $sheet->toArray(null, false, true, true);

            $insert = [];
            foreach ($data as $index => $row) {
                if ($index > 1) {
                    $insert[] = [
                        'barang_id'     => $row['A'],
                        'user_id'       => $row['B'],
                        'stok_tanggal'  => $row['C'],
                        'stok_jumlah'   => $row['D'],
                        'created_at'    => now()
                    ];
                }
            }

            if (!empty($insert)) {
                Stok::insertOrIgnore($insert);
                return response()->json([
                    'status' => true,
                    'message' => 'Data stok berhasil diimport'
                ]);
            }

            return response()->json([
                'status' => false,
                'message' => 'Data kosong'
            ]);
        }

        return redirect('/');
    }

    public function export_excel()
    {
        $stok = Stok::with(['barang', 'user'])->get();

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Barang');
        $sheet->setCellValue('C1', 'User');
        $sheet->setCellValue('D1', 'Tanggal');
        $sheet->setCellValue('E1', 'Jumlah');

        $row = 2;
        foreach ($stok as $index => $s) {
            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $s->barang->barang_nama ?? '');
            $sheet->setCellValue('C' . $row, $s->user->name ?? '');
            $sheet->setCellValue('D' . $row, $s->stok_tanggal);
            $sheet->setCellValue('E' . $row, $s->stok_jumlah);
            $row++;
        }

        foreach (range('A', 'E') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $filename = 'Data_Stok_' . now()->format('Y-m-d_His') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    public function export_pdf()
    {
        $stok = Stok::with(['barang', 'user'])->get();
        $pdf = Pdf::loadView('stok.export_pdf', compact('stok'));
        $pdf->setPaper('a4', 'portrait');
        return $pdf->stream('Data_Stok_' . now()->format('Y-m-d_His') . '.pdf');
    }
}
