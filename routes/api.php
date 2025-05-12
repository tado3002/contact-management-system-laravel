<?php

use App\Http\Controllers\AddressController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\ApiAuthMiddleware;
use Illuminate\Support\Facades\Route;

/*Route::get('/user', function (Request $request) {*/
/*    return $request->user();*/
/*})->middleware('auth:sanctum');*/

Route::post('/users', [UserController::class, 'register']);
Route::post('/users/login', [UserController::class, 'login']);

Route::middleware([ApiAuthMiddleware::class])->group(function () {
    // users route
    Route::get('/users/current', [UserController::class, 'get']);
    Route::patch('/users/current', [UserController::class, 'update']);
    Route::delete('/users/logout', [UserController::class, 'logout']);

    // contacts route
    Route::post('/contacts', [ContactController::class, 'create']);
    Route::get('/contacts', [ContactController::class, 'search']);
    Route::get('/contacts/{id}', [ContactController::class, 'get'])->where(['id' => '[0-9]+']);
    Route::put('/contacts/{id}', [ContactController::class, 'update'])->where(['id' => '[0-9]+']);
    Route::delete('/contacts/{id}', [ContactController::class, 'delete'])->where(['id' => '[0-9]+']);

    // address route
    Route::post(
        '/contacts/{idContact}/addresses',
        [AddressController::class, 'create']
    )->where(['idContact' => '[0-9]+']);
    Route::get(
        '/contacts/{idContact}/addresses/{idAddress}',
        [AddressController::class, 'get']
    )->where(['idContact' => '[0-9]+', 'idAddress' => '[0-9]+']);
    Route::get(
        '/contacts/{idContact}/addresses',
        [AddressController::class, 'list']
    )->where(['idContact' => '[0-9]+']);
    Route::put(
        '/contacts/{idContact}/addresses/{idAddress}',
        [AddressController::class, 'update']
    )->where(['idContact' => '[0-9]+', 'idAddress' => '[0-9]+']);
    Route::delete(
        '/contacts/{idContact}/addresses/{idAddress}',
        [AddressController::class, 'delete']
    )->where(['idContact' => '[0-9]+', 'idAddress' => '[0-9]+']);
});
