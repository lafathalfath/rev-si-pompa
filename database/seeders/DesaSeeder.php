<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DesaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $file_path = storage_path('/app/data_region/villages.csv');
        $csv_file = fopen($file_path, 'r');
        fgetcsv($csv_file); // bypass header
        $desa = [];
        $index = 0;
        while (($row = fgetcsv($csv_file)) !== false) {
            if (!empty($desa) && count($desa[$index]) > 12899) $index++;
            $desa[$index][] = [
                'id' => $row[0],
                'kecamatan_id' => $row[1],
                'name' => $row[2]
            ];
        }
        fclose($csv_file);
        foreach ($desa as $data) {
            DB::table('desa')->insert($data);
        }
    }
}
