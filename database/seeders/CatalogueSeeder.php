<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CatalogueSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            CategorySeeder::class,
            CollectionSeeder::class,
            ProductSeeder::class,
        ]);
    }
}
