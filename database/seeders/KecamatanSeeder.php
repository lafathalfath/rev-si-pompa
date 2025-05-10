<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KecamatanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $file_path = storage_path('/app/data_region/districts.csv');
        $csv_file = fopen($file_path, 'r');
        fgetcsv($csv_file); // bypass header
        $kecamatan = [];
        $index = 0;
        while (($row = fgetcsv($csv_file)) !== false) {
            if (!empty($kecamatan) && count($kecamatan[$index]) > 12899) $index++;
            $kecamatan[$index][] = [
                'id' => $row[0],
                'kabupaten_id' => $row[1],
                'name' => $row[2]
            ];
        }
        fclose($csv_file);
        foreach ($kecamatan as $data) {
            DB::table('kecamatan')->insert($data);
        }
    }
}
