<x-layouts.admin :title="$heading.' | Delight Afriq'">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <p class="da-eyebrow">Content</p>
            <h1 class="da-heading mt-3">{{ $heading }}</h1>
        </div>
        <a href="{{ route('admin.homepage-sections.index') }}" class="da-btn-outline">Back to homepage sections</a>
    </div>

    <form method="POST" action="{{ $action }}" class="da-card mt-8 grid gap-5 p-6">
        @csrf
        @if ($method === 'PUT')
            @method('PUT')
        @endif

        <div class="grid gap-5 sm:grid-cols-2">
            <x-form-input name="title" label="Title" :value="$section->title" required />
            <x-form-input name="key" label="Key" :value="$section->key" />
        </div>

        <div>
            <label for="body" class="da-label">Body</label>
            <textarea id="body" name="body" rows="8" class="da-input" required>{{ old('body', $section->body) }}</textarea>
            @error('body')
                <p class="mt-2 text-sm text-red-700" role="alert">{{ $message }}</p>
            @enderror
        </div>

        <div class="grid gap-5 sm:grid-cols-2">
            <x-form-input name="sort_order" label="Sort order" type="number" :value="$section->sort_order ?? 0" />
            <label class="flex items-center gap-3 text-sm text-body">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $section->is_active)) class="h-4 w-4">
                Active
            </label>
        </div>

        <x-button>Save section</x-button>
    </form>
</x-layouts.admin>
