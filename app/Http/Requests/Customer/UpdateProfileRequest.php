<?php

namespace App\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email:rfc', 'max:255', Rule::unique('users', 'email')->ignore($this->user()->id)],
            'phone' => ['nullable', 'string', 'max:30', 'regex:/^\+?[0-9\s\-()]{7,20}$/'],
            'marketing_opt_in' => ['sometimes', 'boolean'],
            'communication_preferences' => ['sometimes', 'array'],
            'communication_preferences.email' => ['sometimes', 'boolean'],
            'communication_preferences.whatsapp' => ['sometimes', 'boolean'],
        ];
    }
}
