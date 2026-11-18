<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email:rfc', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:30', 'regex:/^\+?[0-9\s\-()]{7,20}$/'],
            'password' => ['required', 'confirmed', Password::min(12)->letters()->numbers()],
            'marketing_opt_in' => ['sometimes', 'boolean'],
            'communication_preferences' => ['sometimes', 'array'],
            'communication_preferences.email' => ['sometimes', 'boolean'],
            'communication_preferences.whatsapp' => ['sometimes', 'boolean'],
        ];
    }
}
