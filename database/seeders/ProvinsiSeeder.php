<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProvinsiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $file_path = storage_path('/app/data_region/provinces.csv');
        $csv_file = fopen($file_path, 'r');
        fgetcsv($csv_file); // bypass header
        $provinsi = [];
        $index = 0;
        while (($row = fgetcsv($csv_file)) !== false) {
            if (!empty($provinsi) && count($provinsi[$index]) > 12899) $index++;
            $provinsi[$index][] = [
                'id' => $row[0],
                'name' => $row[2]
            ];
        }
        fclose($csv_file);
        foreach ($provinsi as $data) {
            DB::table('provinsi')->insert($data);
        }
    }
}
