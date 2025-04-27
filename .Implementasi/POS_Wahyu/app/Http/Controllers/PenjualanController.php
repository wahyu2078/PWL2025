<?php

namespace App\Http\Controllers;

use App\Models\Penjualan;
use App\Models\PenjualanDetail;
use App\Models\Barang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Yajra\DataTables\Facades\DataTables;

class PenjualanController extends Controller
{
    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Daftar Penjualan',
            'list' => ['Home', 'Penjualan']
        ];

        $page = (object) [
            'title' => 'Daftar penjualan yang tercatat dalam sistem'
        ];

        $activeMenu = 'penjualan';

        return view('penjualan.index', compact('breadcrumb', 'page', 'activeMenu'));
    }

    public function list(Request $request)
    {
        $penjualan = Penjualan::with('user')
            ->select([
                'penjualan_id',
                'user_id',
                'pembeli',
                'penjualan_kode',
                'penjualan_tanggal',
                DB::raw('(SELECT SUM(harga * jumlah) FROM t_penjualan_detail WHERE t_penjualan_detail.penjualan_id = t_penjualan.penjualan_id) as total_harga')
            ]);

        return DataTables::of($penjualan)
            ->addIndexColumn()
            ->addColumn('user_nama', function ($stok) {
                return $stok->user->nama ?? '-';
            })
            ->addColumn('total_harga', function ($row) {
                return number_format($row->total_harga ?? 0, 0, ',', '.');
            })
            ->addColumn('aksi', function ($row) {
                $btn = '<button onclick="modalAction(\'' . url('/penjualan/' . $row->penjualan_id . '/edit_ajax') . '\')" class="btn btn-warning btn-sm"><i class="fa fa-edit"></i></button> ';
                $btn .= '<button onclick="modalAction(\'' . url('/penjualan/' . $row->penjualan_id . '/delete_ajax') . '\')" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i></button>';
                return $btn;
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }


    public function create_ajax()
    {
        $barangs = Barang::all();
        return view('penjualan.create_ajax', compact('barangs'));
    }

    public function store_ajax(Request $request)
    {
        $request->merge(['user_id' => auth()->user()->user_id]);
        $request->merge(['penjualan_tanggal' => now()->format('Y-m-d H:i:s')]);
        $request->merge(['penjualan_kode' => 'PJ' . now()->format('YmdHis')]);

        $rules = [
            'user_id' => 'required|exists:m_user,user_id',
            'pembeli' => 'required|string|max:100',
            'penjualan_kode' => 'required|string|unique:t_penjualan,penjualan_kode',
            'penjualan_tanggal' => 'required|date',
            'barang_id.*' => 'required|exists:m_barang,barang_id',
            'harga.*' => 'required|numeric|min:0',
            'jumlah.*' => 'required|integer|min:1'
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'msgField' => $validator->errors()
            ]);
        }

        DB::beginTransaction();
        try {
            $penjualan = Penjualan::create([
                'user_id' => auth()->user()->user_id,
                'pembeli' => $request->pembeli,
                'penjualan_kode' => $request->penjualan_kode,
                'penjualan_tanggal' => $request->penjualan_tanggal
            ]);

            foreach ($request->barang_id as $i => $barangId) {
                $barang = Barang::find($barangId);
                $barang->update([
                    'stok' => $barang->stok - $request->jumlah[$i]
                ]);

                PenjualanDetail::create([
                    'penjualan_id' => $penjualan->penjualan_id,
                    'barang_id' => $barangId,
                    'harga' => $request->harga[$i],
                    'jumlah' => $request->jumlah[$i]
                ]);
            }

            DB::commit();
            return response()->json(['status' => true, 'message' => 'Data penjualan berhasil disimpan']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'message' => 'Gagal menyimpan data']);
        }
    }

    public function edit_ajax($id)
    {
        $penjualan = Penjualan::with('detail')->findOrFail($id);
        $barangs = Barang::all();
        return view('penjualan.edit_ajax', compact('penjualan', 'barangs'));
    }

    public function update_ajax(Request $request, $id)
    {
        $rules = [
            'pembeli' => 'required|string|max:100',
            'penjualan_kode' => "required|string|unique:t_penjualan,penjualan_kode,$id,penjualan_id",
            'barang_id.*' => 'required|exists:m_barang,barang_id',
            'harga.*' => 'required|numeric|min:0',
            'jumlah.*' => 'required|integer|min:1'
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'msgField' => $validator->errors()
            ]);
        }

        DB::beginTransaction();
        try {
            $penjualan = Penjualan::findOrFail($id);
            $penjualan->update([
                'pembeli' => $request->pembeli,
            ]);

            foreach ($request->barang_id as $i => $barangId) {
                $barang = Barang::find($barangId);
                $barang->update([
                    'stok' => $barang->stok + $request->jumlah[$i]
                ]);
            }

            PenjualanDetail::where('penjualan_id', $id)->delete();

            foreach ($request->barang_id as $i => $barangId) {
                $barang = Barang::find($barangId);
                $barang->update([
                    'stok' => $barang->stok - $request->jumlah[$i]
                ]);

                PenjualanDetail::create([
                    'penjualan_id' => $id,
                    'barang_id' => $barangId,
                    'harga' => $request->harga[$i],
                    'jumlah' => $request->jumlah[$i]
                ]);
            }

            DB::commit();
            return response()->json(['status' => true, 'message' => 'Data penjualan berhasil diperbarui']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'message' => 'Gagal memperbarui data']);
        }
    }

    public function confirm_ajax(string $id)
    {
        $penjualan = Penjualan::find($id);
        return view('penjualan.confirm_ajax', compact('penjualan'));
    }

    public function delete_ajax(Request $request, $id)
    {
        if ($request->ajax()) {
            $penjualan = Penjualan::with('detail')->find($id);
            if ($penjualan) {
                foreach ($penjualan->detail ?? [] as $detail) {
                    $barang = Barang::find($detail->barang_id);
                    $barang->update([
                        'stok' => $barang->stok + $detail->jumlah
                    ]);
                }
                PenjualanDetail::where('penjualan_id', $id)->delete();
                $penjualan->delete();

                return response()->json(['status' => true, 'message' => 'Data berhasil dihapus']);
            } else {
                return response()->json(['status' => false, 'message' => 'Data tidak ditemukan']);
            }
        }

        return redirect('/');
    }

    public function export_excel()
    {
        $penjualan = Penjualan::with(['user', 'detail.barang'])
            ->withSum('detail as total_harga', DB::raw('harga * jumlah'))
            ->get();

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'ID');
        $sheet->setCellValue('B1', 'Kode Penjualan');
        $sheet->setCellValue('C1', 'Tanggal');
        $sheet->setCellValue('D1', 'Pembeli');
        $sheet->setCellValue('E1', 'Total Harga');
        $sheet->setCellValue('F1', 'User Pembuat');

        $row = 2;

        foreach ($penjualan as $p) {
            // Baris utama penjualan
            $sheet->setCellValue('A' . $row, $p->penjualan_id);
            $sheet->setCellValue('B' . $row, $p->penjualan_kode);
            $sheet->setCellValue('C' . $row, $p->penjualan_tanggal);
            $sheet->setCellValue('D' . $row, $p->pembeli);
            $sheet->setCellValue('E' . $row, $p->total_harga);
            $sheet->setCellValue('F' . $row, $p->user->nama ?? '');
            $row++;

            // Header detail barang
            $sheet->setCellValue('B' . $row, 'No');
            $sheet->setCellValue('C' . $row, 'Nama Barang');
            $sheet->setCellValue('D' . $row, 'Harga');
            $sheet->setCellValue('E' . $row, 'Jumlah');
            $sheet->setCellValue('F' . $row, 'Subtotal');
            $row++;

            foreach ($p->detail as $i => $d) {
                $sheet->setCellValue('B' . $row, $i + 1);
                $sheet->setCellValue('C' . $row, $d->barang->barang_nama ?? '-');
                $sheet->setCellValue('D' . $row, $d->harga);
                $sheet->setCellValue('E' . $row, $d->jumlah);
                $sheet->setCellValue('F' . $row, $d->harga * $d->jumlah);
                $row++;
            }

            // Spasi antar penjualan
            $row++;
        }

        foreach (range('A', 'F') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $filename = 'Data_Penjualan_' . now()->format('Y-m-d_His') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }


    public function export_pdf()
    {
        $penjualan = Penjualan::with(['user', 'detail.barang'])
            ->withSum('detail as total_harga', DB::raw('harga * jumlah'))
            ->get();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('penjualan.export_pdf', compact('penjualan'));
        $pdf->setPaper('a4', 'portrait');
        return $pdf->stream('Laporan_Penjualan_' . now()->format('Y-m-d_His') . '.pdf');
    }
}
