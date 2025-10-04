<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'nama_depan' => 'Admin',
            'nama_belakang' => 'User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'tanggal_lahir' => '2002-01-01',
            'jenis_kelamin' => 'Laki-laki',
        ]);
    }
}
