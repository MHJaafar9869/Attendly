<?php

namespace Modules\Core\Http\Requests\Auth;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rules\Password;
use Modules\Core\Rules\StrongPassword;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'phone' => 'sometimes|nullable|unique:users,phone|regex:/^\+?[0-9]{7,15}$/',
            'password' => [
                'required',
                'confirmed',
                Password::defaults(),
                new StrongPassword(name: $this->input('first_name') . ' ' . $this->input('last_name')),
            ],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['role' => 6]);
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Validation errors',
            'errors' => $validator->errors(),
        ], 422));
    }
}
