<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactCreateRequest;
use App\Http\Requests\ContactUpdateRequest;
use App\Http\Resources\ContactCollection;
use App\Http\Resources\ContactResource;
use App\Models\Contact;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContactController extends Controller
{
    public function create(ContactCreateRequest $contactCreateRequest): JsonResponse
    {
        $user = Auth::user();

        $data = $contactCreateRequest->validated();

        $contact = new Contact($data);
        $contact->user_id = $user->id;
        $contact->save();

        return (new ContactResource($contact))->response()->setStatusCode(201);
    }

    public function get(int $id): ContactResource
    {
        $user = Auth::user();
        $contact = Contact::where('id', $id)->where('user_id', $user->id)->first();

        if (!$contact) {
            $this->throwNotFoundError();
        }

        return new ContactResource($contact);
    }

    public function update(int $id, ContactUpdateRequest $contactUpdateRequest): ContactResource
    {
        $user = Auth::user();
        $contact = Contact::where('id', $id)->where('user_id', $user->id)->first();

        if (!$contact) {
            throw new HttpResponseException(response()->json([
                'errors' => [
                    'message' => ['not found!']
                ]
            ], 404));
        }
        $data = $contactUpdateRequest->validated();
        if (empty($data)) {
            $this->throwDataRequestEmptyError();
        }



        $contact->fill(
            $data
        );
        $contact->save();

        return new ContactResource($contact);
    }

    public function delete(int $id): JsonResponse
    {
        $user = Auth::user();
        $contact = Contact::where('id', $id)->where('user_id', $user->id)->first();
        if (!$contact) $this->throwNotFoundError();

        $contact->delete();
        return response()->json(['data' => true], 200);
    }

    public function search(Request $request): ContactCollection
    {
        $user = Auth::user();
        $page = $request->input('page', 1);
        $size = $request->input('size', 10);

        $contacts = Contact::query()->where('user_id', $user->id);

        if (!$contacts) $this->throwNotFoundError();

        $contacts = $contacts->where(function (Builder $builder) use ($request) {
            $name = $request->input('name');
            if ($name) {
                $builder->where(function (Builder $builder) use ($name) {
                    $builder->orWhere('first_name', 'like', '%' . $name . '%');
                    $builder->orWhere('last_name', 'like', '%' . $name . '%');
                });
            }
            $email = $request->input('email');
            if ($email) {
                $builder->where('email', 'like', '%' . $email . '%');
            }
            $phone = $request->input('phone');
            if (!empty($phone)) {
                $builder->where('phone', 'like', '%' . $phone . '%');
            }
        });

        $contacts = $contacts->paginate(perPage: $size, page: $page);
        return new ContactCollection($contacts);
    }

    private function throwNotFoundError()
    {
        throw new HttpResponseException(response()->json([
            'errors' => [
                'message' => ['not found!']
            ]
        ], 404));
    }

    private function throwDataRequestEmptyError()
    {
        throw new HttpResponseException(response()->json([
            'errors' => [
                'message' => ['no data provide!']
            ]
        ], 400));
    }
}
