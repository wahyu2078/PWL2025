<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KategoriSeeder extends Seeder
{
    public function run()
    {
        DB::table('m_kategori')->insert([
            ['nama_kategori' => 'Makanan'],
            ['nama_kategori' => 'Minuman'],
            ['nama_kategori' => 'Kecantikan'],
            ['nama_kategori' => 'Kesehatan'],
            ['nama_kategori' => 'Peralatan Rumah']
        ]);
    }
}


