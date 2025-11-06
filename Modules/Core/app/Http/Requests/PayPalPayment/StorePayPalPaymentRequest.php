<?php

namespace Modules\Core\Http\Requests\PayPalPayment;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StorePayPalPaymentRequest extends FormRequest
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
            'user_id' => ['required', 'string', 'max:255', 'exists:users,id'],
            'payable_id' => ['required', 'string', 'max:255'],
            'payable_type' => ['required', 'string', 'max:255'],
            'paypal_transaction_id' => ['required', 'string', 'max:255'],
            'amount_cents' => ['required', 'integer'],
            'currency' => ['required', 'string', 'max:255'],
            'product_data' => ['nullable', 'array'],
            'status_id' => ['required', 'integer', 'exists:statuses,id'],
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
