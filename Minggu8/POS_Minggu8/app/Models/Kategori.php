<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kategori extends Model
{
    use HasFactory;

    protected $table = 'm_kategori'; // Nama tabel
    protected $primaryKey = 'kategori_id'; // Primary key
    public $timestamps = true; // Menggunakan timestamps (created_at, updated_at)

    protected $fillable = [
        'kategori_kode',
        'kategori_nama',
    ];
}