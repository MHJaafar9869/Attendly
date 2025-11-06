<?php

namespace Modules\Core\Http\Requests\Auth;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Modules\Core\Rules\ImageMime;

class StoreProfilePictureRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'image' => [
                'image',
                'mimes:jpg,png,jpeg',
                'max:2048',
                'required',
                new ImageMime,
            ],
            'type' => 'sometimes|string',
        ];
    }

    public function messages(): array
    {
        return [
            'image.required' => 'Please upload a profile image.',
            'image.image' => 'The uploaded file must be a valid image.',
            'image.mimes' => 'Only JPG and PNG images are allowed.',
            'image.max' => 'The image size may not exceed 2 MB.',
            'type.string' => 'The type field must be a valid string.',
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = jwtGuard()->user();
        $routeUser = $this->route('user');

        return $user && $routeUser && ((string) $user->id === (string) $routeUser->id || $user->hasRole('super_admin'));
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
