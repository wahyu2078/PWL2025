<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    use HasFactory;

    protected $table = 'm_barang'; // Nama tabel
    protected $primaryKey = 'barang_id'; // Primary key
    public $timestamps = true; // Menggunakan timestamps (created_at, updated_at)

    protected $fillable = [
        'barang_kode',
        'barang_nama',
        'harga_beli',
        'harga_jual',
    ];
}