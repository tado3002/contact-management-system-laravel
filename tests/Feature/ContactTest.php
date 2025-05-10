<?php

use App\Models\Contact;
use App\Models\User;
use Database\Seeders\ContactSeeder;
use Database\Seeders\SearcContacthSeed;
use Database\Seeders\UserSeeder;
use Illuminate\Database\Eloquent\Builder;

use function Pest\Laravel\delete;
use function Pest\Laravel\get;
use function Pest\Laravel\post;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertNull;

describe('search contact', function () {
    it('success', function () {
        $this->seed([UserSeeder::class, SearcContacthSeed::class]);
        $user = getUserSeed();
        $contacts = getContacts($user->id);

        $res = get(
            '/api/contacts',
            headers: [
                'Authorization' => $user->token
            ]
        )->assertOk()->json();
        assertEquals($contacts['data'], $res['data']);
    });
    it('success with search by name', function () {
        $this->seed([UserSeeder::class, SearcContacthSeed::class]);
        $user = getUserSeed();
        $contacts = getContacts($user->id, params: ['name' => 'first']);

        $res = get('/api/contacts?name=first', headers: [
            'Authorization' => $user->token
        ])->assertOk()->json();
        assertEquals($contacts['data'], $res['data']);
    });
    it('success search by email', function () {
        $this->seed([UserSeeder::class, SearcContacthSeed::class]);
        $user = getUserSeed();
        $contacts = getContacts($user->id, params: ['email' => '1']);
        $res = get(
            '/api/contacts?email=1',
            headers: ['Authorization' => $user->token]
        )->assertOk()->json();
        assertEquals($contacts['data'], $res['data']);
    });
    it('success search by phone', function () {
        $this->seed([UserSeeder::class, SearcContacthSeed::class]);
        $user = getUserSeed();
        $contacts = getContacts($user->id, params: ['phone' => '18']);
        $res = get(
            '/api/contacts?phone=18',
            headers: ['Authorization' => $user->token]
        )->assertOk()->json();
        assertEquals($contacts['data'], $res['data']);
    });
    it('failed cause user token is empty', function () {
        get(
            '/api/contacts?name=first',
            headers: [
                'Authorization' => 'salah'
            ]
        )->assertUnauthorized();
    });
})->only();

describe('create contact', function () {
    it('success', function () {
        $this->seed(UserSeeder::class);
        $data = [
            'first_name' => 'person1',
            'last_name' => 'michigan',
            'email' => 'thisisemail@gmail.com',
            'phone' => '0824243889'
        ];
        post('/api/contacts', $data, headers: [
            'Authorization' => 'test'
        ])->assertStatus(201)->assertJson(['data' => $data]);
    });

    it('failed cause body request empty', function () {
        $this->seed(UserSeeder::class);
        $data = [
            'first_name' => '',
        ];
        post('/api/contacts', $data, headers: [
            'Authorization' => 'test'
        ])->assertStatus(400);
    });

    it('failed cause token is not set in header', function () {
        $data = [
            'first_name' => 'person1',
            'last_name' => 'michigan',
            'email' => 'thisisemail@gmail.com',
            'phone' => '0824243889'
        ];
        post('/api/contacts', $data)->assertStatus(401)->assertJson(
            [
                'errors' =>
                ['message' => ['unauthorized']]
            ]
        );
    });
});


describe('get contact by id', function () {
    it('success', function () {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $this->get('/api/contacts/' . $contact->id, headers: [
            'Authorization' => 'test'
        ])->assertStatus(200)->assertJson(['data' => [
            'first_name' => $contact->first_name,
            'last_name' => $contact->last_name,
            'email' => $contact->email,
            'phone' => $contact->phone
        ]]);
    });
    it('failed cause contact id not found', function () {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $this->get('/api/contacts/' . ($contact->id + 1), headers: [
            'Authorization' => 'test'
        ])->assertStatus(404)->assertJson([
            'errors' => [
                'message' => [
                    'not found!'
                ]
            ]
        ]);
    });
    it('failed cause token is invalid', function () {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $this->get('/api/contacts/' . $contact->id, headers: [
            'Authorization' => 'salah'
        ])->assertStatus(401)->assertJson(
            [
                'errors' =>
                ['message' => ['unauthorized']]
            ]
        );
    });
});


describe('update contact by id', function () {
    it('success', function () {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();
        $newContact = [
            'first_name' => 'stalin',
            'phone' => '082146796695'
        ];

        $this->put(
            '/api/contacts/' . $contact->id,
            [
                'first_name' => $newContact['first_name'],
                'phone' => $newContact['phone']
            ],
            headers: [
                'Authorization' => 'test'
            ]
        )->assertStatus(200)->assertJson(['data' => [
            'first_name' => $newContact['first_name'],
            'last_name' => $contact->last_name,
            'email' => $contact->email,
            'phone' => $newContact['phone']
        ]]);
    });
    it('failed cause body request empty', function () {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $this->put('/api/contacts/' . ($contact->id), headers: [
            'Authorization' => 'test'
        ])->assertStatus(400)->assertJson([
            'errors' => [
                'message' => [
                    'no data provide!'
                ]
            ]
        ]);
    });
    it('failed cause contact id not found', function () {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $this->put('/api/contacts/' . ($contact->id + 1), headers: [
            'Authorization' => 'test'
        ])->assertStatus(404)->assertJson([
            'errors' => [
                'message' => [
                    'not found!'
                ]
            ]
        ]);
    });
    it('failed cause token is invalid', function () {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $this->put('/api/contacts/' . $contact->id, headers: [
            'Authorization' => 'salah'
        ])->assertStatus(401)->assertJson(
            [
                'errors' =>
                ['message' => ['unauthorized']]
            ]
        );
    });
});

describe('delete', function () {
    it('success', function () {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::limit(1)->first();

        delete('/api/contacts/name=first' . $contact->id, headers: [
            'Authorization' => 'test'
        ])->assertOk()->assertJson(['data' => true]);


        $deletedContact = Contact::limit(1)->first();
        assertNull($deletedContact);
    });
    it('failed cause contact id not found', function () {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::limit(1)->first();

        delete('/api/contacts/' . ($contact->id + 1), headers: [
            'Authorization' => 'test'
        ])->assertNotFound();
    });
    it('failed cause token is invalid', function () {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::limit(1)->first();

        delete('/api/contacts/' . $contact->id, headers: [
            'Authorization' => 'testing'
        ])->assertUnauthorized();
    });
});

function getUserSeed()
{
    return User::where('username', 'tado3002')->first();
}



function getContacts(int $userId, int $page = 1, int $size = 10, array $params = [])
{
    $query = Contact::where('user_id', $userId)->select(['id', 'first_name', 'last_name', 'email', 'phone']);

    $contacts = $query->where(function (Builder $builder) use ($params) {
        if (array_key_exists('name', $params)) {
            $name = $params['name'];
            $builder->where(function (Builder $builder) use ($name) {
                $builder->orWhere('first_name', 'like', '%' . $name . '%');
                $builder->orWhere('last_name', 'like', '%' . $name . '%');
            });
        }
        if (array_key_exists('email', $params)) {
            $email = $params['email'];
            $builder->where('email', 'like', '%' . $email . '%');
        }
        if (array_key_exists('phone', $params)) {
            $phone = $params['phone'];
            $builder->where('phone', 'like', '%' . $phone . '%');
        }
    });

    $contacts = $contacts->paginate(perPage: $size, page: $page);
    return $contacts->toArray();
}
