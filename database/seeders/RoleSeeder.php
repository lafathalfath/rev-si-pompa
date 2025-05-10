<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['id' => 1, 'name' => 'admin'],
            ['id' => 2, 'name' => 'pj_nasional'],
            ['id' => 3, 'name' => 'pj_provinsi'],
            ['id' => 4, 'name' => 'pj_kabupaten'],
            ['id' => 5, 'name' => 'pj_kecamatan']
        ];

        DB::table('role')->insert($data);
    }
}
