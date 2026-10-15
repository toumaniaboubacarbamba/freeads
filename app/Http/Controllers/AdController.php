<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAdRequest;
use App\Http\Requests\UpdateAdRequest;
use App\Models\Ad;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class AdController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware(['auth', 'verified'], except: ['index', 'show']),
        ];
    }

    public function index(Request $request)
{
    $filters = $request->validate([
        'q'            => ['nullable', 'string', 'max:100'],
        'categories'   => ['nullable', 'array'],
        'categories.*' => ['integer'],
        'location'     => ['nullable', Rule::in(config('akwaba.locations'))],
        'price_min'    => ['nullable', 'integer', 'min:0'],
        'price_max'    => ['nullable', 'integer', 'min:0'],
        'condition'    => ['nullable', Rule::in(array_keys(config('akwaba.conditions')))],
        'sort'         => ['nullable', Rule::in(['latest', 'price_asc', 'price_desc'])],
    ]);

    $query = Ad::with(['category', 'user', 'photos'])->filter($filters);

    match ($filters['sort'] ?? 'latest') {
        'price_asc'  => $query->orderBy('price'),
        'price_desc' => $query->orderByDesc('price'),
        default      => $query->latest(),
    };

    $ads = $query->paginate(9)->withQueryString();
    $categories = Category::withCount('ads')->orderBy('name')->get();

    return view('ads.index', compact('ads', 'categories'));
}

    public function mine(Request $request)
{
    $ads = $request->user()->ads()
        ->with(['category', 'user', 'photos'])
        ->latest()
        ->paginate(8);

    return view('ads.mine', compact('ads'));
}

    public function create()
    {
        $categories = Category::orderBy('name')->get();

        return view('ads.create', compact('categories'));
    }

    public function store(StoreAdRequest $request)
    {
        $ad = $request->user()->ads()->create($request->safe()->except('photos'));

        $this->storePhotos($ad, $request->file('photos', []));

        return redirect()->route('ads.show', $ad)->with('status', 'Annonce publiée avec succès !');
    }

    public function show(Ad $ad)
{
    $ad->load(['category', 'user', 'photos']);

    $sellerAds = $ad->user->ads()
        ->whereKeyNot($ad->id)
        ->with('photos')
        ->latest()
        ->take(3)
        ->get();

    return view('ads.show', compact('ad', 'sellerAds'));
}

    public function edit(Ad $ad)
    {
        Gate::authorize('update', $ad);

        $ad->load('photos');
        $categories = Category::orderBy('name')->get();

        return view('ads.edit', compact('ad', 'categories'));
    }

    public function update(UpdateAdRequest $request, Ad $ad)
    {
        $removeIds = $request->input('remove_photos', []);
        $newFiles = $request->file('photos', []);

        $remaining = $ad->photos()->whereNotIn('id', $removeIds)->count();
        $total = $remaining + count($newFiles);

        if ($total < 1 || $total > 5) {
            return back()->withInput()->withErrors(['photos' => 'Une annonce doit avoir entre 1 et 5 photos.']);
        }

        $ad->update($request->safe()->except(['photos', 'remove_photos']));

        foreach ($ad->photos()->whereIn('id', $removeIds)->get() as $photo) {
            Storage::disk(config('akwaba.photo_disk'))->delete($photo->path);
            $photo->delete();
        }

        $this->storePhotos($ad, $newFiles);

        return redirect()->route('ads.show', $ad)->with('status', 'Annonce modifiée.');
    }

    public function destroy(Ad $ad)
    {
        Gate::authorize('delete', $ad);

        foreach ($ad->photos as $photo) {
            Storage::disk(config('akwaba.photo_disk'))->delete($photo->path);
        }

        $ad->delete(); // les lignes ad_photos sont supprimées en cascade

        return redirect()->route('ads.mine')->with('status', 'Annonce supprimée.');
    }

    private function storePhotos(Ad $ad, array $files): void
    {
        $position = ((int) $ad->photos()->max('position')) + 1;

        foreach ($files as $file) {
            $ad->photos()->create([
                'path'     => $file->store('ads', config('akwaba.photo_disk')),
                'position' => $position++,
            ]);
        }
    }
}
