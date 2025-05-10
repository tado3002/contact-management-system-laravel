<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\Contact;
use Illuminate\Database\Seeder;

class AddressSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $contact = Contact::limit(1)->first();

        Address::create([
            'street' => 'buyut sateri',
            'city' => 'Pasuruan',
            'province' => 'Jtim',
            'country' => 'Indonesia',
            'postal_code' => '241231',
            'contact_id' => $contact->id

        ]);
    }
}
