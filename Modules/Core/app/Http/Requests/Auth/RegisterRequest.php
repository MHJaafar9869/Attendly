<?php

namespace Modules\Core\Http\Requests\Auth;

use App\Rules\StrongPassword;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rules\Password;

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
            'first_name' => ['required', 'string'],
            'last_name' => ['required', 'string'],
            'email' => ['required', 'email', 'unique:users,email'],
            'phone' => ['sometimes', 'nullable', 'regex:/^\+?[0-9]{7,15}$/', 'unique:users,phone'],
            'role_id' => ['sometimes', 'nullable', 'integer', 'exists:roles,id'],
            'type_id' => ['sometimes', 'nullable', 'integer', 'exists:types,id'],
            'password' => ['required', 'confirmed', Password::defaults(), new StrongPassword(username: $this->input('first_name').' '.$this->input('last_name'))],
        ];
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
