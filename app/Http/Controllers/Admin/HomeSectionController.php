<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\HomeSectionRequest;
use App\Models\HomeSection;
use App\Support\ImageUpload;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class HomeSectionController extends Controller
{
    public function index(): View
    {
        $sections = HomeSection::query()
            ->orderBy('sort_order')
            ->get();

        return view('admin.home-sections.index', compact('sections'));
    }

    public function edit(HomeSection $homeSection): View
    {
        return view('admin.home-sections.edit', ['section' => $homeSection]);
    }

    public function update(HomeSectionRequest $request, HomeSection $homeSection): RedirectResponse
    {
        $data = $request->safe()->except(['image', 'carousel', 'remove_carousel']);

        if ($request->hasFile('image')) {
            if ($homeSection->isStoredUploadPath($homeSection->image_path)) {
                Storage::disk('public')->delete($homeSection->image_path);
            }

            $data['image_path'] = ImageUpload::store($request->file('image'), 'home-sections', 4096, 2400);
        }

        if ($homeSection->key === HomeSection::KEY_HERO) {
            $carouselPaths = array_values(array_filter($homeSection->carousel_paths ?? []));

            foreach ($request->input('remove_carousel', []) as $index) {
                $index = (int) $index;
                if (! array_key_exists($index, $carouselPaths)) {
                    continue;
                }

                $path = $carouselPaths[$index];
                if ($homeSection->isStoredUploadPath($path)) {
                    Storage::disk('public')->delete($path);
                }

                unset($carouselPaths[$index]);
            }

            $carouselPaths = array_values($carouselPaths);

            foreach ($request->file('carousel', []) as $file) {
                if (! $file) {
                    continue;
                }

                $carouselPaths[] = ImageUpload::store($file, 'home-sections/carousel', 4096, 2400);
            }

            $data['carousel_paths'] = $carouselPaths;

            if (empty($data['image_path'] ?? $homeSection->image_path) && $carouselPaths !== []) {
                $data['image_path'] = $carouselPaths[0];
            }
        }

        $homeSection->update($data);

        return redirect()
            ->route('admin.home-sections.index')
            ->with('success', $homeSection->name.' updated successfully.');
    }
}
