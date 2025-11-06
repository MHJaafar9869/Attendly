<?php

namespace Modules\Core\Http\Requests\Auth;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class VerifyOtpRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'otp' => [
                'required',
                'string',
                'digits:6',
                'regex:/^[0-9]{6}$/',
            ],
            'remember' => 'sometimes|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'otp.required' => 'OTP is required',
            'otp.digits' => 'OTP must be exactly 6 digits',
            'otp.regex' => 'OTP must contain only numbers',
        ];
    }

    /**
     * Sanitize OTP input
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('otp')) {
            $this->merge([
                'otp' => preg_replace('/\D/', '', $this->input('otp')),
            ]);
        }
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return jwtGuard()->user()?->email_verified_at !== null;
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
