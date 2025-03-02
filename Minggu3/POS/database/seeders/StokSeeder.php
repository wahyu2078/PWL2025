<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StokSeeder extends Seeder
{
    public function run()
    {
        $stok = [];
        for ($i = 1; $i <= 10; $i++) {
            $stok[] = [
                'barang_id' => $i,
                'user_id' => 1, // Gantilah dengan user_id yang ada
                'stok_tanggal' => now(),
                'stok_jumlah' => rand(10, 100),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('t_stok')->insert($stok);
    }
}
