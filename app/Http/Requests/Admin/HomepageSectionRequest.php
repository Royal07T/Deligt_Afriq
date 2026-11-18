<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class HomepageSectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $section = $this->route('homepageSection');
        $keyRule = Rule::unique('homepage_sections', 'key');

        if ($section) {
            $keyRule->ignore($section->id);
        }

        return [
            'key' => ['nullable', 'string', 'max:80', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $keyRule],
            'title' => ['required', 'string', 'max:150'],
            'body' => ['required', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->filled('title') && ! $this->filled('key')) {
            $this->merge(['key' => Str::slug($this->string('title')->toString())]);
        }

        if ($this->input('key') === '') {
            $this->merge(['key' => null]);
        }
    }
}
