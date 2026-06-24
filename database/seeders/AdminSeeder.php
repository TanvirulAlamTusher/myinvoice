<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
     public function run(): void
    {
        $user = User::create([
            'name' => 'Tusher',
            'phone' => '01628224514',
            'email' => 'tanvirulalam15@gmail.com',
            'password' => Hash::make('1234'),
        ]);

        // assign admin role
        $user->assignRole('admin');
    }
}
