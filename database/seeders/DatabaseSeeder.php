<?php

namespace Database\Seeders;

use Database\Seeders\CategorySeeder;
use Database\Seeders\SourceSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            CategorySeeder::class,
            SourceSeeder::class,
        ]);
    }
}
