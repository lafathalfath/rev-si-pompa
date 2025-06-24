<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = [
            ['id' => 1, 'name' => 'usulan'],
            ['id' => 2, 'name' => 'ditolak'],
            ['id' => 3, 'name' => 'diterima'],
            ['id' => 4, 'name' => 'diverifikasi']
        ];

        DB::table('status')->insert($statuses);
    }
}
