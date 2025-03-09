<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KategoriSeeder extends Seeder
{
    public function run()
    {
        $kategori = [
            ['kategori_kode' => 'ELEC', 'kategori_nama' => 'Elektronik'],
            ['kategori_kode' => 'FASH', 'kategori_nama' => 'Fashion'],
            ['kategori_kode' => 'FOOD', 'kategori_nama' => 'Makanan'],
            ['kategori_kode' => 'HEALTH', 'kategori_nama' => 'Kesehatan'],
            ['kategori_kode' => 'SPORT', 'kategori_nama' => 'Olahraga']
        ];

        DB::table('m_kategori')->insert($kategori);
    }
}
