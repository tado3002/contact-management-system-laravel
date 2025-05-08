<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserRegisterRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

use function Pest\Laravel\json;

class UserController extends Controller
{
    public function register(UserRegisterRequest $userRegisterRequest): JsonResponse
    {

        $data = $userRegisterRequest->validated();

        // throw conflict if username exist
        if (User::where('username', $data['username'])->count() == 1) {
            throw new HttpResponseException(response([
                'errors' => [
                    'username' => ['username already used!']
                ]
            ], 400));
        }

        $user = new User($data);

        // hash passowrd
        $user->password = Hash::make($data['password']);
        $user->save();

        return (new UserResource($user))->response()->setStatusCode(201);
    }

    public function login(UserLoginRequest $userLoginRequest): UserResource
    {
        $data = $userLoginRequest->validated();

        $user = User::where('username', $data['username'])->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            throw new HttpResponseException(response([
                'errors' => [
                    'message' => ['username or password is wrong!']
                ]
            ], 401));
        }

        $user->token = Str::uuid()->toString();
        $user->save();
        return new UserResource($user);
    }

    public function get(Request $request): UserResource
    {

        $user = Auth::user();
        return new UserResource($user);
    }

    public function update(UserUpdateRequest $userUpdateRequest): UserResource
    {
        $data = $userUpdateRequest->validated();

        $user = Auth::user();

        if (isset($data['name'])) {
            $user->name = $data['name'];
        }

        if (isset($data['password'])) {
            $user->password = Hash::make($data['password']);
        }
        $user->save();
        return new UserResource($user);
    }

    public function logout(Request $request): JsonResponse
    {
        $user = Auth::user();
        $user->token = null;
        $user->save();

        return response()->json(['data' => true])->setStatusCode(200);
    }
}
