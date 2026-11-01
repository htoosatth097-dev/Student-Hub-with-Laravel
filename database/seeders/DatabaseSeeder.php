<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /** Seed demo academic data only. Privileged accounts must be created explicitly. */
    public function run(): void
    {
        $this->call(AcademicDataSeeder::class);
    }
}
