<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Mahasiswa;
use App\Models\Dosen;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@simkara.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        $dosen1 = User::create([
            'name' => 'Dr. Ahmad Wijaya',
            'email' => 'ahmad@simkara.com',
            'password' => Hash::make('password123'),
            'role' => 'dosen',
            'is_active' => true,
        ]);

        Dosen::create([
            'user_id' => $dosen1->id,
            'nip' => '198501151990011001',
            'departemen' => 'Teknik Informatika',
            'jabatan' => 'Lektor Kepala',
        ]);

        $mahasiswa1 = User::create([
            'name' => 'Billie Izzat',
            'email' => 'billie@simkara.com',
            'password' => Hash::make('password123'),
            'role' => 'mahasiswa',
            'is_active' => true,
        ]);

        Mahasiswa::create([
            'user_id' => $mahasiswa1->id,
            'nim' => '2201082001',
            'angkatan' => '2022',
            'program_studi' => 'Teknik Informatika',
        ]);

        $mahasiswa2 = User::create([
            'name' => 'Siti Nurhaliza',
            'email' => 'siti@simkara.com',
            'password' => Hash::make('password123'),
            'role' => 'mahasiswa',
            'is_active' => true,
        ]);

        Mahasiswa::create([
            'user_id' => $mahasiswa2->id,
            'nim' => '2201082002',
            'angkatan' => '2022',
            'program_studi' => 'Teknik Informatika',
        ]);
    }
}
