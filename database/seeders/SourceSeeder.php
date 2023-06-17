<?php

namespace Database\Seeders;

use App\Models\Sources;
use Illuminate\Database\Seeder;

class SourceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $sources = [
            ['name' => 'The New York Times'],
            ['name' => 'The Guardian'],
            ['name' => 'NewsAPI'],
        ];

        Sources::insert($sources);
    }
}
