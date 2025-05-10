<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class AddressUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() != null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'street' => ['sometimes', 'max:100'],
            'city' => ['sometimes', 'max:100'],
            'province' => ['sometimes', 'max:100'],
            'country' => ['sometimes', 'max:100'],
            'postal_code' => ['sometimes', 'max:100'],
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        return new HttpResponseException(response()->json([
            'errors' => $validator->getMessageBag()
        ], 400));
    }
}
