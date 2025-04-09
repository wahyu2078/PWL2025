<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PenjualanSeeder extends Seeder
{
    public function run()
    {
        $penjualan = [];
        for ($i = 1; $i <= 10; $i++) {
            $penjualan[] = [
                'user_id' => 1, // Sesuaikan dengan user yang ada
                'pembeli' => 'Pelanggan ' . $i,
                'penjualan_kode' => 'PJL00' . $i,
                'penjualan_tanggal' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('t_penjualan')->insert($penjualan);
    }
}
