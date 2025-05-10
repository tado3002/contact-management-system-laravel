<?php

namespace Database\Seeders;

use App\Models\Contact;
use App\Models\User;
use Illuminate\Database\Seeder;

class ContactSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::where('username', 'tado3002')->first();
        Contact::create([
            'first_name' => 'adolfff',
            'last_name' => 'hitler',
            'email' => 'thirdreich@gmail.com',
            'phone' => '0842141414',
            'user_id' => $user->id
        ]);
    }
}
