<?php

namespace Modules\Core\Http\Requests\Payment;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdatePaymentRequest extends FormRequest
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
            'owner_id' => ['sometimes', 'required', 'string', 'max:255'],
            'user_id' => ['nullable', 'sometimes', 'string', 'max:255', 'exists:users,id'],
            'stripe_payment_intent_id' => ['nullable', 'sometimes', 'string', 'max:255'],
            'amount' => ['sometimes', 'required', 'integer'],
            'currency' => ['sometimes', 'required', 'string', 'max:255'],
            'product_data' => ['nullable', 'sometimes', 'array'],
            'status' => ['sometimes', 'required', 'string', 'max:255'],
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
