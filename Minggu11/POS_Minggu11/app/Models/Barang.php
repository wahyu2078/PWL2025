<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Barang extends Model
{
    use HasFactory;

    protected $table = 'm_barang';
    protected $primaryKey = 'barang_id';

    protected $fillable = [
        'kategori_id',
        'barang_kode',
        'barang_nama',
        'barang_foto',  // tambahkan ini
        'harga_beli',
        'harga_jual'
    ];

    public function kategori(): BelongsTo 
    {
        return $this->belongsTo(Kategori::class, 'kategori_id', 'kategori_id');
    }

    // Accessor untuk barang_foto
    protected function barangFoto(): Attribute
    {
        return Attribute::make(
            get: fn($barangFoto) => $barangFoto ? url('/storage/barang/' . $barangFoto) : null
        );
    }
}
