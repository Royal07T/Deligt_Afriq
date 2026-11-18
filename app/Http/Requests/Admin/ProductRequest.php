<?php

namespace App\Http\Requests\Admin;

use App\Support\Catalogue\ProductStatus;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $product = $this->route('product');

        $skuRule = Rule::unique('products', 'sku');
        $slugRule = Rule::unique('products', 'slug');

        if ($product) {
            $skuRule->ignore($product->id);
            $slugRule->ignore($product->id);
        }

        $imageRule = Rule::exists('product_images', 'id')
            ->where('product_id', $product?->id ?? 0);

        return [
            'category_id' => [
                'required',
                'integer',
                Rule::exists('categories', 'id')->where(fn ($query) => $query->where('is_active', true)->whereNull('deleted_at')),
            ],
            'name' => ['required', 'string', 'max:160'],
            'slug' => ['nullable', 'string', 'max:180', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $slugRule],
            'sku' => ['required', 'string', 'max:80', $skuRule],
            'short_description' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0', 'max:999999999999.99'],
            'compare_at_price' => ['nullable', 'numeric', 'min:0', 'gt:price'],
            'currency' => ['required', 'string', 'size:3'],
            'status' => ['required', Rule::in(ProductStatus::ALL)],
            'is_featured' => ['sometimes', 'boolean'],
            'metadata' => ['nullable', 'array'],
            'collections' => ['sometimes', 'array'],
            'collections.*' => [
                'integer',
                Rule::exists('collections', 'id')->where(fn ($query) => $query->where('is_active', true)->whereNull('deleted_at')),
            ],
            'inventory' => $product ? ['sometimes', 'array'] : ['required', 'array'],
            'inventory.quantity' => ['required_with:inventory', 'integer', 'min:0'],
            'inventory.reserved' => ['nullable', 'integer', 'min:0'],
            'inventory.low_stock_threshold' => ['nullable', 'integer', 'min:0'],
            'inventory.track_inventory' => ['sometimes', 'boolean'],
            'images' => ['sometimes', 'array', 'max:6'],
            'images.*' => ['image', 'mimes:jpeg,jpg,png,webp', 'max:4096'],
            'remove_images' => ['sometimes', 'array'],
            'remove_images.*' => ['integer', $imageRule],
            'primary_image_id' => ['nullable', 'integer', $imageRule],
        ];
    }

    protected function prepareForValidation(): void
    {
        $merge = [];

        if ($this->input('slug') === '') {
            $merge['slug'] = null;
        }

        if ($this->input('primary_image_id') === '') {
            $merge['primary_image_id'] = null;
        }

        if ($this->input('currency')) {
            $merge['currency'] = Str::upper($this->input('currency'));
        }

        $this->merge($merge);
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $inventory = $this->input('inventory');
            $isCreate = $this->route('product') === null;

            if ($isCreate && ! is_array($inventory)) {
                $validator->errors()->add('inventory', 'Inventory details are required.');

                return;
            }

            if (is_array($inventory) && ! array_key_exists('quantity', $inventory)) {
                $validator->errors()->add('inventory.quantity', 'A stock quantity is required.');
            }
        });
    }
}
