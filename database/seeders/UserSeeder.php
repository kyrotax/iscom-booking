<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin ISCOM
        User::updateOrCreate(
            ['email' => 'admin@iscom.org'],
            [
                'name' => 'Admin ISCOM',
                'password' => Hash::make('password123'),
                'nim' => 'ADMIN001',
                'major' => 'Sistem Informasi',
                'semester' => 8,
                'whatsapp' => '081234567890',
                'role' => 'admin',
            ]
        );

        // Mahasiswa Utama (Demo - Dimas Arya Pratama)
        User::updateOrCreate(
            ['email' => 'dimas@student.ac.id'],
            [
                'name' => 'Dimas Arya Pratama',
                'password' => Hash::make('password123'),
                'nim' => '22081010045',
                'major' => 'Sistem Informasi',
                'semester' => 3,
                'whatsapp' => '0812-3456-7890',
                'role' => 'mahasiswa',
            ]
        );

        // Mahasiswa Tambahan (sesuai daftar di mockup)
        $students = [
            [
                'name' => 'Siti Rahmawati',
                'email' => 'siti.rahma@student.ac.id',
                'nim' => '22081010012',
                'major' => 'Sistem Informasi',
                'semester' => 3,
                'whatsapp' => '081233445566',
            ],
            [
                'name' => 'Muhammad Farhan',
                'email' => 'farhan@student.ac.id',
                'nim' => '23081010088',
                'major' => 'Teknologi Informasi',
                'semester' => 3,
                'whatsapp' => '081255667788',
            ],
            [
                'name' => 'Anisa Maharani',
                'email' => 'anisa@student.ac.id',
                'nim' => '22081010034',
                'major' => 'Informatika',
                'semester' => 3,
                'whatsapp' => '081277889900',
            ],
            [
                'name' => 'Budi Santoso',
                'email' => 'budi.santoso@student.ac.id',
                'nim' => '21081010099',
                'major' => 'Sistem Informasi',
                'semester' => 5,
                'whatsapp' => '081299001122',
            ],
        ];

        foreach ($students as $stu) {
            User::updateOrCreate(
                ['email' => $stu['email']],
                array_merge($stu, [
                    'password' => Hash::make('password123'),
                    'role' => 'mahasiswa',
                ])
            );
        }
    }
}
