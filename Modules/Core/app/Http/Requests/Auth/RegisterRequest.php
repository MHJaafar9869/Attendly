<?php

namespace Modules\Core\Http\Requests\Auth;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rules\Password;
use Modules\Core\Enums\RoleIDEnum;
use Modules\Core\Enums\Status\StatusIDEnum;
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

    public function prepareForValidation(): void
    {
        $path = $this->path();
        $data = $this->all();

        if (str_contains($path, 'api/v1/auth/teachers/register')) {
            $statusId = StatusIDEnum::TEACHER_PENDING->value;
            $roleId = RoleIDEnum::TEACHER->value;
        } elseif (str_contains($path, 'api/v1/auth/students/register')) {
            $statusId = StatusIDEnum::STUDENT_PENDING->value;
            $roleId = RoleIDEnum::STUDENT->value;
        }

        if (isset($data['email'])) {
            $data['email'] = strtolower(trim($data['email']));
        }

        if (isset($data['first_name'])) {
            $data['first_name'] = trim($data['first_name']);
        }

        if (isset($data['last_name'])) {
            $data['last_name'] = trim($data['last_name']);
        }

        $data['role_id'] = $roleId;
        $data['status_id'] = $statusId;

        $this->merge($data);
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
            'password' => [
                'required',
                'confirmed',
                Password::defaults(),
                new StrongPassword(name: $this->input('first_name') . ' ' . $this->input('last_name')),
            ],
            'role_id' => 'required|integer|exists:roles,id',
            'status_id' => 'required|integer|exists:statuses,id',
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
