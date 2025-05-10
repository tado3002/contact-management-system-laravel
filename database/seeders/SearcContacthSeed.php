<?php

namespace Database\Seeders;

use App\Models\Contact;
use App\Models\User;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class SearcContacthSeed extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::where('username', 'tado3002')->first();
        /*$faker = Faker::create();*/
        /*// using faker*/
        /*for ($i = 0; $i < 50; $i++) {*/
        /*    Contact::create([*/
        /*        'first_name' => $faker->firstName,*/
        /*        'last_name' => $faker->lastName,*/
        /*        'phone' => $faker->phoneNumber,*/
        /*        'email' => $faker->email,*/
        /*        'user_id' => $user->id*/
        /*    ]);*/
        /*}*/

        for ($i = 0; $i < 25; $i++) {
            Contact::create([
                'first_name' => 'first' . $i,
                'last_name' => 'last' . $i,
                'phone' => '0808211' . $i,
                'email' => "muh.tado{$i}@gmail.com",
                'user_id' => $user->id
            ]);
        }
    }
}
