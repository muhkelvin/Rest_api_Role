<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Payment;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
         User::factory(5)->create();

         Book::factory(22)->create();

//         Payment::factory(10)->create();
    }
}
