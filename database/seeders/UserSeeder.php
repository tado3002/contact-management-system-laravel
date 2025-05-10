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
        $data = [
            'username' => 'tado3002',
            'password' => Hash::make('mypassword'),
            'name' => 'tado',
            'token' => 'test'

        ];
        User::create($data);
    }
}
