<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\HomeSectionRequest;
use App\Models\HomeSection;
use App\Support\HomeSectionSchema;
use App\Support\ImageUpload;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Throwable;

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
        if ($homeSection->key === HomeSection::KEY_HERO) {
            HomeSectionSchema::ensureCarouselPathsColumn();
            $homeSection->refresh();
        }

        return view('admin.home-sections.edit', ['section' => $homeSection]);
    }

    public function update(HomeSectionRequest $request, HomeSection $homeSection): RedirectResponse
    {
        $data = $request->safe()->except(['image', 'carousel', 'replace_carousel', 'remove_carousel']);

        try {
            if ($request->hasFile('image')) {
                if ($homeSection->isStoredUploadPath($homeSection->image_path)) {
                    Storage::disk('public')->delete($homeSection->image_path);
                }

                $data['image_path'] = ImageUpload::store($request->file('image'), 'home-sections', 4096, 2400);
            }

            if ($homeSection->key === HomeSection::KEY_HERO) {
                if (! HomeSectionSchema::ensureCarouselPathsColumn()) {
                    return back()
                        ->withInput()
                        ->with('error', 'Could not create the carousel_paths database column. In phpMyAdmin run: ALTER TABLE home_sections ADD COLUMN carousel_paths LONGTEXT NULL;');
                }

                $carouselPaths = $this->syncHeroCarousel(
                    $homeSection,
                    $this->uploadedFiles($request, 'carousel'),
                    $request->file('replace_carousel') ?? [],
                    $request->input('remove_carousel', []),
                );

                $data['carousel_paths'] = $carouselPaths;

                if (empty($data['image_path'] ?? $homeSection->image_path) && $carouselPaths !== []) {
                    $data['image_path'] = $carouselPaths[0];
                }
            }
        } catch (Throwable $exception) {
            report($exception);

            return back()
                ->withInput()
                ->with('error', 'Could not save images. Check that storage/app/public is writable and run php artisan storage:link on the server.');
        }

        $homeSection->update($data);

        return redirect()
            ->route('admin.home-sections.edit', $homeSection)
            ->with('success', $homeSection->name.' updated successfully.');
    }

    /**
     * @param  list<UploadedFile>  $newFiles
     * @param  array<int|string, UploadedFile|null>  $replaceFiles
     * @param  list<int|string>  $removeIndexes
     * @return list<string>
     */
    private function syncHeroCarousel(
        HomeSection $homeSection,
        array $newFiles,
        array $replaceFiles,
        array $removeIndexes,
    ): array {
        $carouselPaths = $homeSection->normalizedCarouselPaths();

        if ($carouselPaths === []) {
            $carouselPaths = HomeSection::defaultHeroCarouselPaths();
        }

        foreach ($replaceFiles as $index => $file) {
            if (! $file instanceof UploadedFile || ! $file->isValid()) {
                continue;
            }

            $index = (int) $index;

            if (! array_key_exists($index, $carouselPaths)) {
                continue;
            }

            $previousPath = $carouselPaths[$index];

            if ($homeSection->isStoredUploadPath($previousPath)) {
                Storage::disk('public')->delete($previousPath);
            }

            $carouselPaths[$index] = ImageUpload::store($file, 'home-sections/carousel', 4096, 2400);
        }

        $removeIndexes = collect($removeIndexes)
            ->map(fn ($index) => (int) $index)
            ->unique()
            ->sortDesc()
            ->values()
            ->all();

        foreach ($removeIndexes as $index) {
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

        foreach ($newFiles as $file) {
            if (! $file instanceof UploadedFile || ! $file->isValid()) {
                continue;
            }

            $carouselPaths[] = ImageUpload::store($file, 'home-sections/carousel', 4096, 2400);
        }

        return $carouselPaths;
    }

    /**
     * @return list<UploadedFile>
     */
    private function uploadedFiles(HomeSectionRequest $request, string $key): array
    {
        $files = $request->file($key);

        if ($files === null) {
            return [];
        }

        return array_values(array_filter(
            Arr::wrap($files),
            fn ($file) => $file instanceof UploadedFile,
        ));
    }
}
