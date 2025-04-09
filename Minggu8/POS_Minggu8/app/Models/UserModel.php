<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;

class UserModel extends Authenticatable
{
    use HasFactory;

    protected $table = 'm_user';
    protected $primaryKey = 'user_id';
    protected $fillable = [
        'username',
        'password',
        'nama',
        'level_id',
        'foto', 
        'created_at',
        'updated_at',
    ];
    protected $hidden = ['password'];
    protected $casts = [
        'password' => 'hashed',
    ];

    /**
     * Relasi ke tabel level
     */
    public function level(): BelongsTo
    {
        return $this->belongsTo(LevelModel::class, 'level_id', 'level_id');
    }

    /**
     * Mendapatkan nama role dari relasi level
     */
    public function getRoleName(): string
    {
        return $this->level ? $this->level->level_nama : '-';
    }

    /**
     * Cek apakah user memiliki role tertentu
     */
    public function hasRole($role): bool
    {
        return $this->level && $this->level->level_kode == $role;
    }

    /**
     * Mendapatkan kode role user
     */
    public function getRole(): string
    {
        return $this->level ? $this->level->level_kode : '-';
    }
}
