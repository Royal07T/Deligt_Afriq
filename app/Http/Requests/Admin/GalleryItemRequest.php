<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class GalleryItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $isCreate = $this->route('galleryItem') === null;

        return [
            'title' => ['required', 'string', 'max:150'],
            'category' => ['nullable', 'string', 'max:80'],
            'image' => [$isCreate ? 'required' : 'nullable', 'file', 'mimes:jpeg,jpg,png,webp', 'max:4096'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
