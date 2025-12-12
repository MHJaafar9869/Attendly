<?php

namespace Modules\Core\Http\Requests\Auth;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Modules\Core\Enums\Status\StatusIDEnum;

class VerifyOtpRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $userSlug = $this->route('user');

        return (bool) sanctumUser()?->slug_name === $userSlug;
    }

    /**
     * Sanitize OTP input
     */
    protected function prepareForValidation(): void
    {
        $path = $this->path();

        if (str_contains($path, 'api/v1/auth/teachers')) {
            $statusId = StatusIDEnum::TEACHER_PENDING->value;
        } elseif (str_contains($path, 'api/v1/auth/students')) {
            $statusId = StatusIDEnum::STUDENT_PENDING->value;
        }

        if ($this->has('otp')) {
            $this->merge([
                'otp' => preg_replace('/\D/', '', $this->input('otp')), // Remove non-numeric characters
                'status_id' => $statusId,
            ]);
        }
    }

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
            'status_id' => 'required|integer|exists:statuses,id',
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

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Validation errors',
            'errors' => $validator->errors(),
        ], 422));
    }
}
