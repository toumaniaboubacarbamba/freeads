@php
    $input = 'mt-1 block w-full rounded-xl border-gray-300 focus:border-ci-orange focus:ring-ci-orange';
    $isEdit = isset($ad);
@endphp

<form method="POST"
      action="{{ $isEdit ? route('ads.update', $ad) : route('ads.store') }}"
      enctype="multipart/form-data"
      class="space-y-5 rounded-2xl border border-orange-100 bg-white p-6">
    @csrf
    @if ($isEdit) @method('PUT') @endif

    <div>
        <label for="title" class="font-semibold">Titre</label>
        <input id="title" name="title" type="text" value="{{ old('title', $ad->title ?? '') }}" required class="{{ $input }}">
        @error('title') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div class="grid gap-5 sm:grid-cols-2">
        <div>
            <label for="category_id" class="font-semibold">Catégorie</label>
            <select id="category_id" name="category_id" required class="{{ $input }}">
                <option value="">Choisir…</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @selected(old('category_id', $ad->category_id ?? null) == $category->id)>{{ $category->name }}</option>
                @endforeach
            </select>
            @error('category_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="condition" class="font-semibold">État</label>
            <select id="condition" name="condition" required class="{{ $input }}">
                @foreach (config('akwaba.conditions') as $key => $label)
                    <option value="{{ $key }}" @selected(old('condition', $ad->condition ?? 'used') === $key)>{{ $label }}</option>
                @endforeach
            </select>
            @error('condition') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
    </div>

    <div class="grid gap-5 sm:grid-cols-2">
        <div>
            <label for="price" class="font-semibold">Prix (FCFA)</label>
            <input id="price" name="price" type="number" min="0" value="{{ old('price', $ad->price ?? '') }}" required class="{{ $input }}">
            @error('price') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="location" class="font-semibold">Localisation</label>
            <select id="location" name="location" required class="{{ $input }}">
                <option value="">Choisir…</option>
                @foreach (config('akwaba.locations') as $place)
                    <option value="{{ $place }}" @selected(old('location', $ad->location ?? null) === $place)>{{ $place }}</option>
                @endforeach
            </select>
            @error('location') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
    </div>

    <div>
        <label for="description" class="font-semibold">Description</label>
        <textarea id="description" name="description" rows="6" required class="{{ $input }}">{{ old('description', $ad->description ?? '') }}</textarea>
        @error('description') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    @if ($isEdit && $ad->photos->isNotEmpty())
        <div>
            <p class="font-semibold">Photos actuelles <span class="text-sm font-normal text-gray-500">(coche pour supprimer)</span></p>
            <div class="mt-2 grid grid-cols-3 gap-3 sm:grid-cols-5">
                @foreach ($ad->photos as $photo)
                    <label class="block cursor-pointer">
                        <img src="{{ $photo->url }}" alt="" class="h-24 w-full rounded-xl object-cover">
                        <span class="mt-1 flex items-center gap-1 text-xs text-red-600">
                            <input type="checkbox" name="remove_photos[]" value="{{ $photo->id }}" class="rounded border-gray-300 text-red-600 focus:ring-red-500"> Supprimer
                        </span>
                    </label>
                @endforeach
            </div>
        </div>
    @endif

    <div>
        <label for="photos" class="font-semibold">{{ $isEdit ? 'Ajouter des photos' : 'Photos (1 à 5)' }}</label>
        <input id="photos" name="photos[]" type="file" accept="image/*" multiple {{ $isEdit ? '' : 'required' }}
               class="mt-1 block w-full rounded-xl border border-gray-300 bg-white p-2 text-sm file:mr-3 file:rounded-full file:border-0 file:bg-orange-100 file:px-4 file:py-2 file:font-semibold file:text-ci-orange">
        <p class="mt-1 text-xs text-gray-500">JPG, PNG ou WEBP, 2 Mo maximum par photo.</p>
        @error('photos') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        @error('photos.*') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div class="flex items-center justify-end gap-3">
        <a href="{{ $isEdit ? route('ads.show', $ad) : route('home') }}" class="text-sm font-semibold text-gray-500 hover:text-ci-brown">Annuler</a>
        <button type="submit" class="rounded-full bg-ci-green px-6 py-2 font-semibold text-white hover:opacity-90">
            {{ $isEdit ? 'Enregistrer' : 'Publier l\'annonce' }}
        </button>
    </div>
</form>
