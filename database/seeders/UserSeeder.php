<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            ['nip' => '1234567890654321','name' => 'Iqna', 'email' => 'iqna@gmail.com', 'phone_number' => '081213131414', 'password' => Hash::make('12121212'), 'role_id' => 1, 'password_changed' => true]
        ];

        DB::table('users')->insert($users);
    }
}
