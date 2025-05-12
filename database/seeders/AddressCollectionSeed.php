<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\Contact;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AddressCollectionSeed extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::where('username', 'tado3002')->first();
        $contact = Contact::where('user_id', $user->id)->first();

        for ($i = 1; $i <= 40; $i++) {
            $data = [
                'street' => 'street' . $i,
                'city' => 'city' . $i,
                'province' => 'province' . $i,
                'country' => 'country' . $i,
                'postal_code' => '6718' . $i,
                'contact_id' => $contact->id
            ];
            Address::create($data);
        }
    }
}
