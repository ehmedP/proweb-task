<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateAccountRequest extends FormRequest
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'currency_code' => 'required|string|size:3|exists:currencies,code',
            'initial_balance' => 'sometimes|numeric|min:0|max:999999.99',
            'daily_limit' => 'sometimes|numeric|min:10|max:10000',
        ];
    }

    public function messages(): array
    {
        return [
            'currency_code.required' => 'Currency code is required',
            'currency_code.size' => 'Currency code must be exactly 3 characters',
            'currency_code.exists' => 'Selected currency is not supported',
            'initial_balance.numeric' => 'Initial balance must be a valid number',
            'initial_balance.min' => 'Initial balance cannot be negative',
            'initial_balance.max' => 'Initial balance cannot exceed 999,999.99',
            'daily_limit.numeric' => 'Daily limit must be a valid number',
            'daily_limit.min' => 'Daily limit must be at least 10',
            'daily_limit.max' => 'Daily limit cannot exceed 10,000',
        ];
    }

    public function prepareForValidation(): void
    {
        $this->merge([
            'currency_code' => strtoupper($this->currency_code ?? ''),
        ]);
    }
}
