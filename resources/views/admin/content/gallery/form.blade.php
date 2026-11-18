<x-layouts.admin :title="$heading.' | Delight Afriq'">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <p class="da-eyebrow">Content</p>
            <h1 class="da-heading mt-3">{{ $heading }}</h1>
        </div>
        <a href="{{ route('admin.gallery.index') }}" class="da-btn-outline">Back to gallery</a>
    </div>

    <form method="POST" action="{{ $action }}" enctype="multipart/form-data" class="da-card mt-8 grid gap-5 p-6">
        @csrf
        @if ($method === 'PUT')
            @method('PUT')
        @endif

        <div class="grid gap-5 sm:grid-cols-2">
            <x-form-input name="title" label="Title" :value="$item->title" required />
            <x-form-input name="category" label="Category" :value="$item->category" />
        </div>

        <div>
            <label for="image" class="da-label">Image</label>
            <input id="image" name="image" type="file" accept="image/jpeg,image/png,image/webp" class="da-input" @if (! $item->exists) required @endif>
            @if ($item->image_path)
                <img src="{{ App\Support\Media\MediaUrl::for($item->image_path) }}" alt="{{ $item->title }}" class="mt-4 h-32 w-full rounded-lg object-cover">
            @endif
            @error('image')
                <p class="mt-2 text-sm text-red-700" role="alert">{{ $message }}</p>
            @enderror
        </div>

        <div class="grid gap-5 sm:grid-cols-2">
            <x-form-input name="sort_order" label="Sort order" type="number" :value="$item->sort_order ?? 0" />
            <label class="flex items-center gap-3 text-sm text-body">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $item->is_active)) class="h-4 w-4">
                Active
            </label>
        </div>

        <x-button>Save gallery item</x-button>
    </form>
</x-layouts.admin>
