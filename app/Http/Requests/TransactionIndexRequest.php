<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransactionIndexRequest extends FormRequest
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
            'account_number' => [
                'required',
                'string',
                'regex:/\d{15}$/',
                'exists:accounts,account_number',
            ],
            'per_page' => [
                'nullable',
                'integer',
                'min:1',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'account_number.required' => 'The account number is required.',
            'account_number.string' => 'The account number must be a string.',
            'account_number.regex' => 'The account number must be 16 digits and start with the number 4.',
            'account_number.exists' => 'The account number does not exist in our records.',

            'per_page.integer' => 'The per page value must be an integer.',
            'per_page.min' => 'The per page value must be at least :min.',
            'per_page.max' => 'The per page value may not be greater than :max.',
        ];
    }
}
