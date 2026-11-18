<x-layouts.admin :title="$heading.' | Delight Afriq'">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <p class="da-eyebrow">Content</p>
            <h1 class="da-heading mt-3">{{ $heading }}</h1>
        </div>
        <a href="{{ route('admin.pages.index') }}" class="da-btn-outline">Back to pages</a>
    </div>

    <form method="POST" action="{{ $action }}" class="da-card mt-8 grid gap-5 p-6">
        @csrf
        @if ($method === 'PUT')
            @method('PUT')
        @endif

        <div class="grid gap-5 sm:grid-cols-2">
            <x-form-input name="title" label="Title" :value="$page->title" required />
            <x-form-input name="slug" label="Slug" :value="$page->slug" />
        </div>

        <x-form-input name="excerpt" label="Excerpt" :value="$page->excerpt" />

        <div>
            <label for="body" class="da-label">Body</label>
            <textarea id="body" name="body" rows="12" class="da-input" required>{{ old('body', $page->body) }}</textarea>
            @error('body')
                <p class="mt-2 text-sm text-red-700" role="alert">{{ $message }}</p>
            @enderror
        </div>

        <div class="grid gap-5 sm:grid-cols-2">
            <x-form-input name="meta_title" label="Meta title" :value="$page->meta_title" />
            <x-form-input name="meta_description" label="Meta description" :value="$page->meta_description" />
        </div>

        <div class="grid gap-5 sm:grid-cols-2">
            <label class="flex items-center gap-3 text-sm text-body">
                <input type="checkbox" name="is_published" value="1" @checked(old('is_published', $page->is_published)) class="h-4 w-4">
                Published
            </label>
            <x-form-input name="published_at" label="Published at" type="datetime-local" :value="old('published_at', $page->published_at?->format('Y-m-d\TH:i'))" />
        </div>

        <x-button>Save page</x-button>
    </form>
</x-layouts.admin>
