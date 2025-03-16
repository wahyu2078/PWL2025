<?php

namespace App\DataTables;

use App\Models\KategoriModel;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class KategoriDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('action', function ($kategori) {
                // Tombol Edit dengan link menuju halaman edit kategori
                return '
                    <a href="' . route('kategori.edit', ['id' => $kategori->kategori_id]) . '" 
                        class="btn btn-warning btn-sm">Edit</a>
                    
                    <!-- Form Hapus dengan konfirmasi -->
                    <form action="' . route('kategori.destroy', ['id' => $kategori->kategori_id]) . '" 
                        method="POST" style="display:inline;">
                        
                        ' . csrf_field() . ' <!-- Token keamanan -->
                        ' . method_field('DELETE') . ' <!-- Metode DELETE -->
                        
                        <button type="submit" class="btn btn-danger btn-sm" 
                            onclick="return confirm(\'Hapus kategori ini?\')">Delete</button>
                    </form>
                ';
            })
            ->rawColumns(['action']) // Pastikan HTML dalam kolom action tidak diubah menjadi teks biasa
            ->setRowId('kategori_id'); // Set ID unik untuk setiap baris tabel
    }
    

    /**
     * Get the query source of dataTable.
     */
    public function query(KategoriModel $model): QueryBuilder
    {
        return $model->newQuery()->select(['kategori_id', 'kategori_kode', 'kategori_nama', 'created_at', 'updated_at']);
    }

    /**
     * Optional method for HTML Builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('kategori-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(1)
            ->selectStyleSingle()
            ->buttons([
                Button::make('excel'),
                Button::make('csv'),
                Button::make('pdf'),
                Button::make('print'),
                Button::make('reset'),
                Button::make('reload')
            ]);
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [
            Column::make('kategori_id')->title('ID'),
            Column::make('kategori_kode')->title('Kode Kategori'),
            Column::make('kategori_nama')->title('Nama Kategori'),
            Column::make('created_at')->title('Dibuat'),
            Column::make('updated_at')->title('Diperbarui'),
            Column::computed('action')
                ->exportable(false)
                ->printable(false)
                ->width(100)
                ->addClass('text-center'),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Kategori_' . date('YmdHis');
    }
}
