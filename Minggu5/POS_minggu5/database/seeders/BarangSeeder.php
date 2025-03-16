<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BarangSeeder extends Seeder
{
    public function run()
    {
        $barang = [];
        for ($i = 1; $i <= 10; $i++) {
            $barang[] = [
                'kategori_id' => rand(1, 5),
                'barang_kode' => 'BRG00' . $i,
                'barang_nama' => 'Barang ' . $i,
                'harga_beli' => rand(1000, 10000),
                'harga_jual' => rand(1100, 12000),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('m_barang')->insert($barang);
    }
}
