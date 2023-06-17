<?php

namespace Database\Seeders;

use App\Models\Categories;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories = [
            ['name' => 'Government'],
            ['name' => 'International News'],
            ['name' => 'Economy'],
            ['name' => 'Technology'],
            ['name' => 'Science'],
            ['name' => 'Health'],
            ['name' => 'Arts'],
            ['name' => 'Entertainment'],
            ['name' => 'Sports'],
            ['name' => 'Education'],
            ['name' => 'Opinion'],
            ['name' => 'Investigations'],
            ['name' => 'Environment'],
            ['name' => 'Business']
        ];

        Categories::insert($categories);
    }
}
