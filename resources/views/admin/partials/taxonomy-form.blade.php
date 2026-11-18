@php
    $isEdit = $model->exists;
    $active = old('is_active', $model->exists ? $model->is_active : true);
@endphp

<form method="POST" action="{{ $action }}" class="da-card max-w-3xl space-y-6 p-6">
    @csrf
    @if ($isEdit)
        @method('PUT')
    @endif

    <x-form-input name="name" label="Name" :value="$model->name" required />
    <x-form-input name="slug" label="Slug (leave blank to generate)" :value="$model->slug" />

    <div>
        <label for="description" class="da-label">Description</label>
        <textarea id="description" name="description" rows="4" class="da-input">{{ old('description', $model->description) }}</textarea>
        @error('description')
            <p class="mt-2 text-sm text-red-700" role="alert">{{ $message }}</p>
        @enderror
    </div>

    <x-form-input name="image_path" label="Image path" :value="$model->image_path" />
    <x-form-input name="sort_order" label="Sort order" type="number" :value="old('sort_order', $model->sort_order ?? 0)" />

    <label class="flex items-center gap-3 text-sm text-ink">
        <input type="hidden" name="is_active" value="0">
        <input type="checkbox" name="is_active" value="1" @checked($active) class="h-4 w-4">
        Active
    </label>

    <div class="flex flex-wrap items-center gap-4">
        <x-button>{{ $isEdit ? 'Save changes' : 'Create '.$label }}</x-button>
        <a href="{{ $cancelRoute }}" class="text-sm font-semibold text-navy-950 underline underline-offset-4">Cancel</a>
    </div>
</form>
