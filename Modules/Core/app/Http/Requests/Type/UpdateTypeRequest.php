<?php

namespace Modules\Core\Http\Requests\Type;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateTypeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Define validation rules for this request.
     */
    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'required', 'string', 'max:255', 'unique:types,name,' . $this->route('type') . ',id'],
            'context' => ['sometimes', 'required', 'string', 'max:255', 'unique:types,context,' . $this->route('type') . ',id'],
            'text_color' => ['sometimes', 'required', 'string', 'max:255'],
            'bg_color' => ['sometimes', 'required', 'string', 'max:255'],
        ];
    }

    /**
     * Customize validation failure response.
     */
    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Validation errors',
            'errors' => $validator->errors(),
        ], 422));
    }
}
