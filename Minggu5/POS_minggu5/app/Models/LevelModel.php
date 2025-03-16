<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LevelModel extends Model
{
    use HasFactory;

    protected $table = 'm_level'; // Pastikan nama tabel sesuai
    protected $primaryKey = 'level_id'; // Sesuaikan primary key dengan database
}
