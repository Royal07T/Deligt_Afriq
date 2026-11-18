<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ContentPageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $page = $this->route('page');
        $slugRule = Rule::unique('content_pages', 'slug');

        if ($page) {
            $slugRule->ignore($page->id);
        }

        return [
            'title' => ['required', 'string', 'max:150'],
            'slug' => ['nullable', 'string', 'max:160', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $slugRule],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'body' => ['required', 'string'],
            'meta_title' => ['nullable', 'string', 'max:150'],
            'meta_description' => ['nullable', 'string', 'max:300'],
            'is_published' => ['sometimes', 'boolean'],
            'published_at' => ['nullable', 'date'],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->filled('title') && ! $this->filled('slug')) {
            $this->merge(['slug' => Str::slug($this->string('title')->toString())]);
        }

        if ($this->input('slug') === '') {
            $this->merge(['slug' => null]);
        }
    }
}
