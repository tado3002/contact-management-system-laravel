<?php

use App\Models\Address;
use App\Models\Contact;
use App\Models\User;
use Database\Seeders\AddressCollectionSeed;
use Database\Seeders\AddressSeeder;
use Database\Seeders\ContactSeeder;
use Database\Seeders\UserSeeder;

use function Pest\Laravel\delete;
use function Pest\Laravel\get;
use function Pest\Laravel\post;
use function Pest\Laravel\put;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertNull;

describe('create address', function () {
    it('success', function () {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $user = getUserSeed();
        $contact = getContact();

        $data = [
            'street' => 'testing',
            'country' => 'Indonesia',
            'postal_code' => '67184'
        ];

        post("/api/contacts/{$contact['id']}/addresses", $data, headers: [
            'Authorization' => $user->token
        ])->assertStatus(201)->json();
    });
    it('failed cause contact id not found', function () {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $user = getUserSeed();
        $contact = getContact();

        $data = [
            'street' => 'testing',
            'country' => 'Indonesia',
            'postal_code' => '67184'
        ];
        $contact['id'] = $contact['id'] + 1;

        post("/api/contacts/{$contact['id']}/addresses", $data, headers: [
            'Authorization' => $user->token
        ])->assertNotFound();
    });
    it('failed cause authorization header not set', function () {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = getContact();

        $data = [
            'street' => 'testing',
            'country' => 'Indonesia',
            'postal_code' => '67184'
        ];

        post("/api/contacts/{$contact['id']}/addresses", $data, headers: [
            'Authorization' => 'salah'
        ])->assertUnauthorized();
    });
    it('failed cause body request is empty', function () {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $user = getUserSeed();
        $contact = getContact();

        $data = [];

        post("/api/contacts/{$contact['id']}/addresses", $data, headers: [
            'Authorization' => $user->token
        ])->assertBadRequest();
    });
});

describe('get address', function () {
    it('success', function () {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $user = getUserSeed();
        $contact = getContact();
        $address = getAddress();

        get("/api/contacts/{$contact->id}/addresses/{$address['id']}", headers: [
            'Authorization' => $user->token
        ])->assertOk();
    });
    it('failed cause address id not found', function () {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $user = getUserSeed();
        $contact = getContact();
        $address = getAddress();
        $addressId = $address['id'] + 1;

        get("/api/contacts/{$contact->id}/addresses/{$addressId}", headers: [
            'Authorization' => $user->token
        ])->assertNotFound();
    });
    it('failed cause contact id not found', function () {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $user = getUserSeed();
        $contact = getContact();
        $contact->id = $contact->id + 1;
        $address = getAddress();

        get("/api/contacts/{$contact->id}/addresses/{$address['id']}", headers: [
            'Authorization' => $user->token
        ])->assertNotFound();
    });
    it('failed cause token is invalid', function () {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $contact = getContact();
        $address = getAddress();

        get("/api/contacts/{$contact->id}/addresses/{$address['id']}", headers: [
            'Authorization' => 'salah'
        ])->assertUnauthorized();
    });
});

describe('get list address by contact id', function () {
    it('success', function () {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressCollectionSeed::class]);
        $user = getUserSeed();
        $contact = getContact();
        $addresses = getAddressCollection($contact->id);
        $res = get("/api/contacts/{$contact->id}/addresses", headers: [
            'Authorization' => $user->token
        ])->assertOk()->json();

        assertEquals($addresses['data'], $res['data']);
    });
    it('success get list address with param', function () {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressCollectionSeed::class]);
        $user = getUserSeed();
        $contact = getContact();
        $page = 2;
        $perPage = 20;
        $addresses = getAddressCollection($contact->id, $page, $perPage);
        $res = get("/api/contacts/{$contact->id}/addresses?page={$page}&perPage={$perPage}", headers: [
            'Authorization' => $user->token
        ])->assertOk()->json();

        assertEquals($addresses['data'], $res['data']);
    });
    it('failed cause contact id not found', function () {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressCollectionSeed::class]);
        $user = getUserSeed();
        $contact = getContact();
        $contactId = $contact->id + 1;
        get("/api/contacts/{$contactId}/addresses", headers: [
            'Authorization' => $user->token
        ])->assertNotFound()->assertJson([
            'errors' => [
                'message' => ['not found!']
            ]
        ]);
    });
    it('failed cause token is invalid', function () {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressCollectionSeed::class]);
        $contact = getContact();
        get("/api/contacts/{$contact->id}/addresses", headers: [
            'Authorization' => 'salah'
        ])->assertUnauthorized()->assertJson([
            'errors' => [
                'message' => ['unauthorized']
            ]
        ]);
    });
});

describe('update address', function () {
    it('success', function () {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $user = getUserSeed();
        $contact = getContact();
        $address = getAddress();

        $dataUpdate = [
            'street' => 'Jl Buyut Sateri',
            'city' => 'Kab Pasuruan'
        ];

        $res = put("/api/contacts/{$contact->id}/addresses/{$address['id']}", $dataUpdate, [
            'Authorization' => $user->token
        ])->assertOk()->json();
        $updatedAddress = getAddress();
        assertEquals($updatedAddress, $res['data']);
    });
    it('failed cause body request empty', function () {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $user = getUserSeed();
        $contact = getContact();
        $address = getAddress();

        put("/api/contacts/{$contact->id}/addresses/{$address['id']}", headers: [
            'Authorization' => $user->token
        ])->assertBadRequest()->assertJson([
            'errors' => [
                'message' => ['no data provide!']
            ]
        ]);
    });
    it('failed cause contact id not found', function () {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $user = getUserSeed();
        $contact = getContact();
        $contact->id = $contact->id + 1;
        $address = getAddress();

        $dataUpdate = [
            'street' => 'Jl Buyut Sateri',
            'city' => 'Kab Pasuruan'
        ];

        put("/api/contacts/{$contact->id}/addresses/{$address['id']}", $dataUpdate, headers: [
            'Authorization' => $user->token
        ])
            ->assertNotFound()->assertJson([
                'errors' => [
                    'message' => ['not found!']
                ]
            ]);
    });
    it('failed cause address id not found', function () {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $user = getUserSeed();
        $contact = getContact();
        $address = getAddress();
        $address['id'] = $address['id'] + 1;

        $dataUpdate = [
            'street' => 'Jl Buyut Sateri',
            'city' => 'Kab Pasuruan'
        ];

        put("/api/contacts/{$contact->id}/addresses/{$address['id']}", $dataUpdate, headers: [
            'Authorization' => $user->token
        ])->assertNotFound()->assertJson([
            'errors' => [
                'message' => ['not found!']
            ]
        ]);
    });
    it('failed cause token is invalid', function () {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $contact = getContact();
        $address = getAddress();

        put("/api/contacts/{$contact->id}/addresses/{$address['id']}", headers: [
            'Authorization' => 'salah'
        ])->assertUnauthorized()->assertJson([
            'errors' => [
                'message' => ['unauthorized']
            ]
        ]);
    });
});

describe('delete addresses', function () {
    it('success', function () {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $user = getUserSeed();
        $contact = getContact();
        $address = getAddress();
        delete("/api/contacts/{$contact->id}/addresses/{$address['id']}", headers: [
            'Authorization' => $user->token
        ])->assertOk()->assertJson([
            'data' => true
        ]);

        assertNull(getAddress());
    });
    it('failed cause contacts id not found', function () {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $user = getUserSeed();
        $contact = getContact();
        $contactId = $contact->id + 1;
        $address = getAddress();
        delete("/api/contacts/{$contactId}/addresses/{$address['id']}", headers: [
            'Authorization' => $user->token
        ])->assertNotFound()->assertJson([
            'errors' => ['message' => ['not found!']]
        ]);
    });
    it('failed cause address id not found', function () {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $user = getUserSeed();
        $contact = getContact();
        $address = getAddress();
        $addressId = $address['id'] + 1;
        delete("/api/contacts/{$contact->id}/addresses/{$addressId}", headers: [
            'Authorization' => $user->token
        ])->assertNotFound()->assertJson([
            'errors' => ['message' => ['not found!']]
        ]);
    });
    it('failed cause token is invalid', function () {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $contact = getContact();
        $address = getAddress();
        delete("/api/contacts/{$contact->id}/addresses/{$address['id']}", headers: [
            'Authorization' => 'salah'
        ])->assertUnauthorized()->assertJson([
            'errors' => ['message' => ['unauthorized']]
        ]);
    });
});



function getUserSeed()
{
    return User::where('username', 'tado3002')->first();
}

function getContact()
{
    return Contact::limit(1)->first();
}
function getAddress()
{
    $address = Address::limit(1)->select(['street', 'city', 'province', 'country', 'postal_code', 'id'])->first();
    return $address == null ? null : $address->toArray();
}
function getAddressCollection(int $contactId, int $page = 1, int $perPage = 10)
{
    $addresses = Address::where('contact_id', $contactId)
        ->select(['street', 'city', 'province', 'country', 'postal_code', 'id'])
        ->paginate(page: $page, perPage: $perPage);
    return $addresses->toArray();
}
