<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddressCreateRequest;
use App\Http\Requests\AddressUpdateRequest;
use App\Http\Resources\AddressCollectionResource;
use App\Http\Resources\AddressResource;
use App\Models\Address;
use App\Models\Contact;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AddressController extends Controller
{
    public function create(int $idContact, AddressCreateRequest $addressCreateRequest): JsonResponse
    {
        $user = Auth::user();

        $contact = Contact::where('id', $idContact)->where('user_id', $user->id)->first();
        if (!$contact) $this->throwNotFoundError();

        $data = $addressCreateRequest->validated();
        $address = new Address($data);
        $address->contact_id = $contact->id;

        $address->save();

        return (new AddressResource($address))->response()->setStatusCode(201);
    }

    public function get(int $idContact, int $idAddress): AddressResource
    {
        $user = Auth::user();
        $contact = Contact::where('user_id', $user->id)->where('id', $idContact)->first();

        if (!$contact) $this->throwNotFoundError();
        $address = Address::where('contact_id', $contact->id)->where('id', $idAddress)->first();
        if (!$address) $this->throwNotFoundError();
        return new AddressResource($address);
    }

    public function list(int $idContact, Request $request): AddressCollectionResource
    {
        $user = Auth::user();
        $contact = Contact::where('id', $idContact)->where('user_id', $user->id)->first();
        if (!$contact) $this->throwNotFoundError();

        $page = $request->input('page', 1);
        $size = $request->input('perPage', 10);
        $addresses = Address::where('contact_id', $contact->id)->paginate(page: $page, perPage: $size);

        return new AddressCollectionResource($addresses);
    }

    public function update(int $idContact, int $idAddress, AddressUpdateRequest $addressUpdateRequest): AddressResource
    {
        $user = Auth::user();

        $contact = Contact::where('user_id', $user->id)->where('id', $idContact)->first();
        if (!$contact) $this->throwNotFoundError();

        $data = $addressUpdateRequest->validated();

        if (empty($data)) $this->throwDataRequestEmpty();

        $address = Address::where('contact_id', $contact->id)->where('id', $idAddress)->first();
        if (!$address) $this->throwNotFoundError();
        $address->fill($data);
        $address->save();

        return new AddressResource($address);
    }

    public function delete(int $idContact, int $idAddress): JsonResponse
    {
        $user = Auth::user();
        $contact = Contact::where('id', $idContact)->where('user_id', $user->id)->first();
        if (!$contact) $this->throwNotFoundError();
        $address = Address::where('id', $idAddress)->where('contact_id', $contact->id)->first();
        if (!$address) $this->throwNotFoundError();
        $address->delete();

        return response()->json([
            'data' => true
        ]);
    }

    private function throwNotFoundError()
    {
        throw new HttpResponseException(response()->json([
            'errors' => [
                'message' => ['not found!']
            ]
        ], 404));
    }
    private function throwDataRequestEmpty()
    {
        throw new HttpResponseException(response()->json([
            'errors' => [
                'message' => ['no data provide!']
            ]
        ], 400));
    }
}
