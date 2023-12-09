<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Doctor;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $specialists = ['Dokter Umum', 'Dokter spesialis penyakit dalam', 'Dokter spesialis anak', 'Dokter spesialis saraf'];

        foreach($specialists as $specialist){
            Doctor::create([
                'specialist' => $specialist
            ]);
        }

        // Create Admin
        User::create([
            'name' => 'Admin 1',
            'email' => 'admin1@test.com',
            'password' => Hash::make('admin123'),
            'jenis_kelamin' => 'Pria',
            'role' => 'admin',
            'telephone' => '08123123123',
            'is_active' => 'Active'
        ]);
    }
}
