<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WithdrawalRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'account_number' => ['required', 'string', 'max:20'],
            'amount' => ['required', 'numeric', 'min:1', 'max:10000'],
            'currency_code' => ['required', 'string', 'size:3', 'in:AZN,USD'],
        ];
    }

    public function messages(): array
    {
        return [
            'account_number.required' => 'Account number is required',
            'amount.required' => 'Amount is required',
            'amount.min' => 'Minimum withdrawal amount is 1',
            'amount.max' => 'Maximum withdrawal amount is 10000',
            'currency_code.required' => 'Currency code is required',
            'currency_code.in' => 'Currency must be either AZN or USD',
        ];
    }
}
