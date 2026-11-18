<?php

namespace App\Http\Requests\Storefront;

use App\Support\Catalogue\ProductStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AddToCartRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product_id' => [
                'required',
                'integer',
                Rule::exists('products', 'id')->where(fn ($query) => $query
                    ->where('status', ProductStatus::ACTIVE)
                    ->whereNull('deleted_at')),
            ],
            'quantity' => ['required', 'integer', 'min:1', 'max:1000'],
        ];
    }
}
