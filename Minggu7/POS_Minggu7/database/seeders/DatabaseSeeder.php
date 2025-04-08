<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            KategoriSeeder::class,
            BarangSeeder::class,
            StokSeeder::class,
            PenjualanSeeder::class,
            PenjualanDetailSeeder::class,
        ]);
    }
}
