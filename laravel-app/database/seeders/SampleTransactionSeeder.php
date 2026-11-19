<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class SampleTransactionSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(DemoDataSeeder::class);
    }
}

