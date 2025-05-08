<?php

use App\Models\User;
use Database\Seeders\UserSeeder;

use function Pest\Laravel\delete;
use function Pest\Laravel\get;
use function Pest\Laravel\patch;
use function Pest\Laravel\postJson;
use function PHPUnit\Framework\assertNotEquals;
use function PHPUnit\Framework\assertNotNull;
use function PHPUnit\Framework\assertNull;

describe(
    'register',
    function () {
        it('successs', function () {
            $data = [
                'username' => 'tado3002',
                'password' => 'mypassword',
                'name' => 'tado'

            ];
            postJson('/api/users', $data)->assertStatus(201)->assertJson(['data' => [
                'username' => $data['username'],
                'name' => $data['name']
            ]]);
        });

        it('failed', function () {
            $data = [
                'username' => '',
                'password' => '',
                'name' => ''
            ];
            postJson('/api/users', $data)->assertStatus(400)->assertJson(['errors' => [
                'username' => [
                    'The username field is required.'
                ],
                'password' => [
                    'The password field is required.'
                ],
                'name' => [
                    'The name field is required.'
                ]
            ]]);
        });

        it('failed cause username already exist', function () {
            $data = [
                'username' => 'tado3002',
                'password' => 'mypassword',
                'name' => 'tado'
            ];
            postJson('/api/users', $data)->assertStatus(201);
            postJson('/api/users', $data)->assertStatus(400)->assertJson(['errors' => [
                'username' => [
                    'username already used!'
                ]
            ]]);
        });
    }
);

describe(
    'login',
    function () {
        it('successs', function () {
            $this->seed(UserSeeder::class);
            $data = [
                'username' => 'tado3002',
                'password' => 'mypassword',

            ];
            postJson('/api/users/login', $data)->assertStatus(200)->assertJson(['data' => [
                'username' => $data['username'],
                'name' => 'tado'
            ]]);

            $user = User::where('username', $data['username'])->first();
            assertNotNull($user->token);
        });

        it('failed cause invalid request', function () {
            $data = [
                'username' => '',
                'password' => '',
            ];
            postJson('/api/users/login', $data)->assertStatus(400)->assertJson(['errors' => [
                'username' => [
                    'The username field is required.'
                ],
                'password' => [
                    'The password field is required.'
                ],
            ]]);
        });

        it('failed cause username or password wrong already exist', function () {
            $this->seed(UserSeeder::class);
            $data = [
                'username' => 'tado3001',
                'password' => 'mypassword',
            ];
            postJson('/api/users/login', $data)->assertStatus(401)->assertJson(['errors' => [
                'message' => [
                    'username or password is wrong!'
                ]
            ]]);
        });
    }
);

describe('get current user', function () {
    it('success', function () {
        $this->seed(UserSeeder::class);
        // login scenario
        $user = User::where('username', 'tado3002')->first();
        $user->token = 'testing';
        $user->save();

        // fetching
        get('/api/users/current', [
            "Authorization" => $user->token
        ])->assertStatus(200)->assertJson([
            "data" => [
                "username" => $user->username,
                "name" => $user->name
            ]
        ]);
    });
    it('failed cause token not in header', function () {
        $this->seed(UserSeeder::class);
        // login scenario
        $user = User::where('username', 'tado3002')->first();
        $user->token = 'testing';
        $user->save();

        // fetching
        get('/api/users/current')->assertStatus(401)->assertJson([
            "errors" => [
                "message" => ["unauthorized"],
            ]
        ]);
    });
    it('failed cause token not in database (facking token)', function () {
        $this->seed(UserSeeder::class);
        // login scenario
        $user = User::where('username', 'tado3002')->first();
        $user->token = 'testing';
        $user->save();

        // fetching
        get('/api/users/current', [
            'Authorization' => 'test'
        ])->assertStatus(401)->assertJson([
            "errors" => [
                "message" => ["unauthorized"],
            ]
        ]);
    });
});


describe('update current user', function () {
    it('success update name', function () {
        $this->seed(UserSeeder::class);
        // login scenario
        $user = User::where('username', 'tado3002')->first();
        $user->token = 'testing';
        $user->save();

        // fetching
        patch('/api/users/current', [
            'name' => 'murtado'
        ], [
            "Authorization" => $user->token
        ])->assertStatus(200)->assertJson([
            "data" => [
                "username" => $user->username,
                "name" => 'murtado'
            ]
        ]);
    });
    it('success update password', function () {
        $this->seed(UserSeeder::class);
        // login scenario
        $user = User::where('username', 'tado3002')->first();
        $user->token = 'testing';
        $user->save();

        // fetching
        patch('/api/users/current', [
            'password' => 'murtado'
        ], [
            "Authorization" => $user->token
        ])->assertStatus(200)->assertJson([
            "data" => [
                "username" => $user->username,
                "name" => 'tado'
            ]
        ]);
        $newUser = User::where('username', 'tado3002')->first();
        assertNotEquals('mypassword', $newUser->password);
    });
    /*it('failed cause empty body request', function () {*/
    /*    $this->seed(UserSeeder::class);*/
    /*    // login scenario*/
    /*    $user = User::where('username', 'tado3002')->first();*/
    /*    $user->token = 'testing';*/
    /*    $user->save();*/
    /**/
    /*    // fetching*/
    /*    get('/api/users/current', [*/
    /*        'Authorization' => 'testing'*/
    /*    ])->assertStatus(200)->assertJson([*/
    /*        "errors" => [*/
    /*            "message" => ["unauthorized"],*/
    /*        ]*/
    /*    ]);*/
    /*});*/
    it('failed cause token not in header', function () {
        $this->seed(UserSeeder::class);
        // login scenario
        $user = User::where('username', 'tado3002')->first();
        $user->token = 'testing';
        $user->save();

        // fetching
        get('/api/users/current')->assertStatus(401)->assertJson([
            "errors" => [
                "message" => ["unauthorized"],
            ]
        ]);
    });
});


describe('logout user', function () {
    it('succes', function () {

        $this->seed(UserSeeder::class);
        $user = User::where('username', 'tado3002')->first();
        $user->token = 'testing';
        $user->save();

        delete('/api/users/logout', headers: [
            'Authorization' => 'testing'
        ])->assertStatus(200)->assertJson(['data' => true]);


        $logoutUser = User::where('username', 'tado3002')->first();
        assertNull($logoutUser->token);
    });
});
