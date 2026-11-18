<?php

namespace App\Http\Requests\Storefront;

use App\Support\Orders\PaymentMethod;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'address_id' => [
                'nullable',
                'integer',
                Rule::exists('addresses', 'id')->where('user_id', $this->user()?->id),
            ],
            'shipping_name' => ['nullable', 'string', 'max:120'],
            'shipping_phone' => ['nullable', 'string', 'max:30', 'regex:/^\+?[0-9\s\-()]{7,20}$/'],
            'shipping_email' => ['nullable', 'email', 'max:180'],
            'shipping_line_1' => ['nullable', 'string', 'max:180'],
            'shipping_line_2' => ['nullable', 'string', 'max:180'],
            'shipping_city' => ['nullable', 'string', 'max:80'],
            'shipping_state' => ['nullable', 'string', 'max:80'],
            'shipping_postal_code' => ['nullable', 'string', 'max:20'],
            'shipping_country' => ['nullable', 'string', 'size:2'],
            'payment_method' => ['required', Rule::in(PaymentMethod::available())],
            'notes' => ['nullable', 'string', 'max:1000'],
            'terms' => ['required', 'accepted'],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->input('shipping_country')) {
            $this->merge(['shipping_country' => Str::upper($this->input('shipping_country'))]);
        }
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if ($this->input('address_id')) {
                return;
            }

            foreach ([
                'shipping_name',
                'shipping_phone',
                'shipping_email',
                'shipping_line_1',
                'shipping_city',
                'shipping_country',
            ] as $field) {
                if (! filled($this->input($field))) {
                    $validator->errors()->add($field, 'This field is required when no saved address is selected.');
                }
            }
        });
    }
}
