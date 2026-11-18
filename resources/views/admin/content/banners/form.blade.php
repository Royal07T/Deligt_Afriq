<x-layouts.admin :title="$heading.' | Delight Afriq'">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <p class="da-eyebrow">Content</p>
            <h1 class="da-heading mt-3">{{ $heading }}</h1>
        </div>
        <a href="{{ route('admin.banners.index') }}" class="da-btn-outline">Back to banners</a>
    </div>

    <form method="POST" action="{{ $action }}" enctype="multipart/form-data" class="da-card mt-8 grid gap-5 p-6">
        @csrf
        @if ($method === 'PUT')
            @method('PUT')
        @endif

        <x-form-input name="title" label="Title" :value="$banner->title" required />
        <x-form-input name="subtitle" label="Subtitle" :value="$banner->subtitle" />

        <div class="grid gap-5 sm:grid-cols-2">
            <x-form-input name="cta_label" label="CTA label" :value="$banner->cta_label" />
            <x-form-input name="cta_url" label="CTA URL" type="url" :value="$banner->cta_url" />
        </div>

        <div>
            <label for="image" class="da-label">Image</label>
            <input id="image" name="image" type="file" accept="image/jpeg,image/png,image/webp" class="da-input" @if (! $banner->exists) required @endif>
            @if ($banner->image_path)
                <img src="{{ App\Support\Media\MediaUrl::for($banner->image_path) }}" alt="{{ $banner->title }}" class="mt-4 h-32 w-full rounded-lg object-cover">
            @endif
            @error('image')
                <p class="mt-2 text-sm text-red-700" role="alert">{{ $message }}</p>
            @enderror
        </div>

        <div class="grid gap-5 sm:grid-cols-2">
            <x-form-input name="sort_order" label="Sort order" type="number" :value="$banner->sort_order ?? 0" />
            <label class="flex items-center gap-3 text-sm text-body">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $banner->is_active)) class="h-4 w-4">
                Active
            </label>
        </div>

        <x-button>Save banner</x-button>
    </form>
</x-layouts.admin>
