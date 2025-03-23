<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Level extends Model
{
    use HasFactory;

    protected $table = 'm_level'; // Nama tabel
    protected $primaryKey = 'level_id'; // Primary key
    public $timestamps = true; // Menggunakan timestamps (created_at, updated_at)

    protected $fillable = [
        'level_kode',
        'level_nama',
    ];
}