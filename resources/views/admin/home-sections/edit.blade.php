@extends('layouts.admin')

@section('heading', 'Edit '.$section->name)

@section('content')
    <form method="POST" action="{{ route('admin.home-sections.update', $section) }}" enctype="multipart/form-data" class="card max-w-3xl space-y-4 p-6">
        @csrf
        @method('PUT')

        @if (in_array($section->key, [\App\Models\HomeSection::KEY_HERO, \App\Models\HomeSection::KEY_CTA, \App\Models\HomeSection::KEY_SHOP_CATEGORY, \App\Models\HomeSection::KEY_NEW_ARRIVALS, \App\Models\HomeSection::KEY_TESTIMONIALS_HEADER]))
            <div>
                <label class="block text-sm font-medium">Eyebrow</label>
                <input type="text" name="eyebrow" value="{{ old('eyebrow', $section->eyebrow) }}" class="input-field">
            </div>
        @endif

        @if (in_array($section->key, [\App\Models\HomeSection::KEY_HERO, \App\Models\HomeSection::KEY_CTA, \App\Models\HomeSection::KEY_SHOP_CATEGORY, \App\Models\HomeSection::KEY_NEW_ARRIVALS, \App\Models\HomeSection::KEY_TESTIMONIALS_HEADER, \App\Models\HomeSection::KEY_DELIVERY_NOTICE]))
            <div>
                <label class="block text-sm font-medium">Title</label>
                <input type="text" name="title" value="{{ old('title', $section->title) }}" class="input-field">
            </div>
        @endif

        @if ($section->key === \App\Models\HomeSection::KEY_HERO)
            <div>
                <label class="block text-sm font-medium">Title highlight (red text)</label>
                <input type="text" name="title_highlight" value="{{ old('title_highlight', $section->title_highlight) }}" class="input-field">
            </div>
        @endif

        @if (in_array($section->key, [\App\Models\HomeSection::KEY_HERO, \App\Models\HomeSection::KEY_CTA, \App\Models\HomeSection::KEY_SHOP_CATEGORY, \App\Models\HomeSection::KEY_NEW_ARRIVALS, \App\Models\HomeSection::KEY_FREE_DELIVERY, \App\Models\HomeSection::KEY_DELIVERY_NOTICE]))
            <div>
                <label class="block text-sm font-medium">Body / description</label>
                <textarea name="body" rows="4" class="input-field">{{ old('body', $section->body) }}</textarea>
                @if ($section->key === \App\Models\HomeSection::KEY_FREE_DELIVERY)
                    <p class="mt-1 text-xs text-brand-muted">Use <code>{currency_symbol}</code> and <code>{threshold}</code> for dynamic values.</p>
                @endif
            </div>
        @endif

        @if (in_array($section->key, [\App\Models\HomeSection::KEY_HERO, \App\Models\HomeSection::KEY_CTA, \App\Models\HomeSection::KEY_SHOP_CATEGORY, \App\Models\HomeSection::KEY_NEW_ARRIVALS]))
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="block text-sm font-medium">Primary button label</label>
                    <input type="text" name="primary_label" value="{{ old('primary_label', $section->primary_label) }}" class="input-field">
                </div>
                <div>
                    <label class="block text-sm font-medium">Primary button URL</label>
                    <input type="text" name="primary_url" value="{{ old('primary_url', $section->primary_url) }}" class="input-field" placeholder="/shop">
                </div>
            </div>
        @endif

        @if (in_array($section->key, [\App\Models\HomeSection::KEY_HERO, \App\Models\HomeSection::KEY_CTA]))
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="block text-sm font-medium">Secondary button label</label>
                    <input type="text" name="secondary_label" value="{{ old('secondary_label', $section->secondary_label) }}" class="input-field">
                </div>
                <div>
                    <label class="block text-sm font-medium">Secondary button URL</label>
                    <input type="text" name="secondary_url" value="{{ old('secondary_url', $section->secondary_url) }}" class="input-field" placeholder="/shop">
                </div>
            </div>
        @endif

        @if ($section->key === \App\Models\HomeSection::KEY_HERO)
            @php
                $carouselPaths = array_values($section->carousel_paths ?? []);
            @endphp

            <div class="space-y-3 rounded-xl border border-neutral-200 p-4">
                <div>
                    <label class="block text-sm font-medium">Hero carousel images</label>
                    <p class="mt-1 text-xs text-brand-muted">These photos rotate on the homepage hero. Upload new slides or remove ones you no longer want.</p>
                </div>

                @if ($carouselPaths !== [])
                    <div class="grid gap-3 sm:grid-cols-2">
                        @foreach ($carouselPaths as $index => $path)
                            <label class="relative block overflow-hidden rounded-lg border border-neutral-200 bg-neutral-50">
                                <img
                                    src="{{ $section->resolvePathUrl($path) }}"
                                    alt="Carousel slide {{ $index + 1 }}"
                                    class="h-36 w-full object-cover"
                                >
                                <span class="absolute inset-x-0 bottom-0 flex items-center gap-2 bg-black/65 px-3 py-2 text-xs text-white">
                                    <input type="checkbox" name="remove_carousel[]" value="{{ $index }}" class="h-4 w-4 rounded border-neutral-300 text-brand-red">
                                    Remove slide {{ $index + 1 }}
                                </span>
                            </label>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-brand-muted">No custom slides yet — defaults will show until you upload images.</p>
                @endif

                <div x-data>
                    <label class="block text-sm font-medium">Add carousel images</label>
                    <input
                        type="file"
                        name="carousel[]"
                        accept="image/*"
                        multiple
                        class="mt-1 w-full text-sm"
                        @change="
                            const preview = $refs.carouselPreview;
                            preview.innerHTML = '';
                            Array.from($event.target.files || []).forEach((file) => {
                                if (!file.type.startsWith('image/')) return;
                                const img = document.createElement('img');
                                img.src = URL.createObjectURL(file);
                                img.className = 'h-28 w-full rounded-lg object-cover';
                                img.alt = file.name;
                                preview.appendChild(img);
                            });
                        "
                    >
                    <div x-ref="carouselPreview" class="mt-3 grid gap-3 sm:grid-cols-2"></div>
                    <p class="mt-1 text-xs text-brand-muted">You can select multiple images at once. Large images are compressed to {{ \App\Support\ImageUpload::targetLabel(4096) }}.</p>
                    @error('carousel')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    @error('carousel.*')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>
        @endif

        <div class="flex items-center gap-2">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" value="1" id="is_active" class="h-4 w-4 rounded border-neutral-300 text-brand-red" @checked(old('is_active', $section->is_active))>
            <label for="is_active" class="text-sm">Show this section on the website</label>
        </div>

        <div class="flex gap-3">
            <button type="submit" class="btn-primary">Save section</button>
            <a href="{{ route('admin.home-sections.index') }}" class="btn-outline px-4 py-2">Cancel</a>
        </div>
    </form>
@endsection
