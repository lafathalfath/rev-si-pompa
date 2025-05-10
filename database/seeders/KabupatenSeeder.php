<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KabupatenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $file_path = storage_path('/app/data_region/regencies.csv');
        $csv_file = fopen($file_path, 'r');
        fgetcsv($csv_file); // bypass header
        $kabupaten = [];
        $index = 0;
        while (($row = fgetcsv($csv_file)) !== false) {
            if (!empty($kabupaten) && count($kabupaten[$index]) > 12899) $index++;
            $kabupaten[$index][] = [
                'id' => $row[0],
                'provinsi_id' => $row[1],
                'name' => $row[2]
            ];
        }
        fclose($csv_file);
        foreach ($kabupaten as $data) {
            DB::table('kabupaten')->insert($data);
        }
    }
}
