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
                $carouselSlides = $section->adminCarouselSlides();
            @endphp

            <div class="space-y-4 rounded-xl border border-neutral-200 p-4">
                <div>
                    <label class="block text-sm font-medium">Hero carousel images</label>
                    <p class="mt-1 text-xs text-brand-muted">These photos rotate on the homepage. Replace a slide, remove it, or add more at the bottom. Click <strong>Save section</strong> when you are done.</p>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    @foreach ($carouselSlides as $slide)
                        <div class="overflow-hidden rounded-lg border border-neutral-200 bg-neutral-50">
                            @if ($slide['url'])
                                <img
                                    src="{{ $slide['url'] }}"
                                    alt="Carousel slide {{ $slide['index'] + 1 }}"
                                    class="h-40 w-full object-cover"
                                >
                            @else
                                <div class="flex h-40 items-center justify-center bg-neutral-100 text-sm text-brand-muted">
                                    Image unavailable
                                </div>
                            @endif

                            <div class="space-y-3 p-3">
                                <div class="flex items-center justify-between gap-2 text-xs text-brand-muted">
                                    <span>Slide {{ $slide['index'] + 1 }}</span>
                                    @if ($slide['is_default'])
                                        <span class="rounded-full bg-neutral-200 px-2 py-0.5">Default</span>
                                    @else
                                        <span class="rounded-full bg-emerald-100 px-2 py-0.5 text-emerald-800">Uploaded</span>
                                    @endif
                                </div>

                                <div>
                                    <label class="block text-xs font-medium text-brand-black">Replace this slide</label>
                                    <input
                                        type="file"
                                        name="replace_carousel[{{ $slide['index'] }}]"
                                        accept="image/*"
                                        class="mt-1 w-full text-xs"
                                        data-preview-target="replace-preview-{{ $slide['index'] }}"
                                        onchange="window.previewHeroSlide(this)"
                                    >
                                    <div id="replace-preview-{{ $slide['index'] }}" class="mt-2 hidden">
                                        <p class="mb-1 text-xs text-brand-muted">New image (save to apply):</p>
                                        <img src="" alt="" class="h-24 w-full rounded-md object-cover">
                                    </div>
                                </div>

                                <label class="flex cursor-pointer items-center gap-2 text-sm">
                                    <input
                                        type="checkbox"
                                        name="remove_carousel[]"
                                        value="{{ $slide['index'] }}"
                                        class="h-4 w-4 rounded border-neutral-300 text-brand-red"
                                    >
                                    Remove this slide
                                </label>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div>
                    <label class="block text-sm font-medium">Add more carousel images</label>
                    <input
                        type="file"
                        name="carousel[]"
                        accept="image/*"
                        multiple
                        class="mt-1 w-full text-sm"
                        data-preview-target="carousel-add-preview"
                        onchange="window.previewHeroSlides(this)"
                    >
                    <div id="carousel-add-preview" class="mt-3 hidden grid gap-3 sm:grid-cols-2"></div>
                    <p class="mt-1 text-xs text-brand-muted">Select one or more images. Large files are compressed to {{ \App\Support\ImageUpload::targetLabel(4096) }}.</p>
                    @error('carousel')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    @error('carousel.*')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    @error('replace_carousel.*')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
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

    @if ($section->key === \App\Models\HomeSection::KEY_HERO)
        @push('scripts')
            <script>
                window.previewHeroSlide = function (input) {
                    const targetId = input.dataset.previewTarget;
                    const wrap = document.getElementById(targetId);
                    const img = wrap?.querySelector('img');

                    if (!wrap || !img || !input.files?.[0]) {
                        return;
                    }

                    img.src = URL.createObjectURL(input.files[0]);
                    wrap.classList.remove('hidden');
                };

                window.previewHeroSlides = function (input) {
                    const targetId = input.dataset.previewTarget;
                    const wrap = document.getElementById(targetId);

                    if (!wrap) {
                        return;
                    }

                    wrap.innerHTML = '';
                    wrap.classList.add('hidden');

                    const files = Array.from(input.files || []).filter((file) => file.type.startsWith('image/'));

                    if (files.length === 0) {
                        return;
                    }

                    const label = document.createElement('p');
                    label.className = 'col-span-full text-xs text-brand-muted';
                    label.textContent = 'New images (save to apply):';
                    wrap.appendChild(label);

                    files.forEach((file) => {
                        const img = document.createElement('img');
                        img.src = URL.createObjectURL(file);
                        img.className = 'h-28 w-full rounded-lg object-cover';
                        img.alt = file.name;
                        wrap.appendChild(img);
                        wrap.classList.remove('hidden');
                    });
                };
            </script>
        @endpush
    @endif
@endsection
