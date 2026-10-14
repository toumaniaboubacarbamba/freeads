@php
    $isEdit = isset($ad);
    $card = 'rounded-2xl border border-outline-variant/50 bg-surface-container-lowest p-6 shadow-[0_2px_8px_rgba(43,30,22,0.04)]';
    $field = 'mt-1 block w-full rounded-xl border-[1.5px] border-outline-variant bg-white px-4 py-3 text-sm focus:border-primary-container focus:ring-4 focus:ring-primary-container/15';
    $label = 'text-sm font-bold';
    $err = 'mt-1 text-sm text-red-600';
    $step = 'grid h-8 w-8 place-items-center rounded-full bg-primary-container text-sm font-bold text-white';
    $conditionIcons = ['new' => 'new_releases', 'good' => 'thumb_up', 'used' => 'build'];
    $currentCondition = old('condition', $ad->condition ?? 'used');
    $titleValue = old('title', $ad->title ?? '');
@endphp

<form method="POST"
      action="{{ $isEdit ? route('ads.update', $ad) : route('ads.store') }}"
      enctype="multipart/form-data"
      class="space-y-6">
    @csrf
    @if ($isEdit) @method('PUT') @endif

    @if ($errors->any())
        <div class="flex items-center gap-2 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
            <span class="material-symbols-outlined">error</span> Certains champs sont à corriger, voir les messages ci-dessous.
        </div>
    @endif

    {{-- 1. Informations générales --}}
    <section class="{{ $card }}" x-data="{ n: {{ mb_strlen($titleValue) }} }">
        <h2 class="mb-5 flex items-center gap-3 text-lg font-bold"><span class="{{ $step }}">1</span> Informations générales</h2>

        <div>
            <div class="flex items-center justify-between">
                <label for="title" class="{{ $label }}">Titre de l'annonce *</label>
                <span class="text-xs text-on-surface-variant"><span x-text="n"></span> / 120</span>
            </div>
            <input id="title" name="title" type="text" maxlength="120" required value="{{ $titleValue }}"
                   @input="n = $event.target.value.length"
                   placeholder="Ex : iPhone 13 Pro 256 Go bleu, parfait état" class="{{ $field }}">
            @error('title') <p class="{{ $err }}">{{ $message }}</p> @enderror
        </div>

        <div class="mt-5">
            <label for="category_id" class="{{ $label }}">Catégorie *</label>
            <select id="category_id" name="category_id" required class="{{ $field }}">
                <option value="">Choisir une catégorie…</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @selected(old('category_id', $ad->category_id ?? null) == $category->id)>{{ $category->name }}</option>
                @endforeach
            </select>
            @error('category_id') <p class="{{ $err }}">{{ $message }}</p> @enderror
        </div>

        <fieldset class="mt-5">
            <legend class="{{ $label }}">État du bien *</legend>
            <div class="mt-2 grid grid-cols-3 gap-3">
                @foreach (config('akwaba.conditions') as $key => $text)
                    <label class="cursor-pointer">
                        <input type="radio" name="condition" value="{{ $key }}" required class="peer sr-only" @checked($currentCondition === $key)>
                        <span class="flex flex-col items-center gap-1 rounded-xl border-2 border-outline-variant bg-white px-3 py-4 text-center text-sm font-semibold transition peer-checked:border-primary-container peer-checked:bg-primary-container/10 peer-checked:text-primary peer-focus-visible:ring-4 peer-focus-visible:ring-primary-container/20">
                            <span class="material-symbols-outlined">{{ $conditionIcons[$key] ?? 'sell' }}</span>
                            {{ $text }}
                        </span>
                    </label>
                @endforeach
            </div>
            @error('condition') <p class="{{ $err }}">{{ $message }}</p> @enderror
        </fieldset>
    </section>

    {{-- 2. Photos --}}
    <section class="{{ $card }}">
        <h2 class="mb-1 flex items-center gap-3 text-lg font-bold"><span class="{{ $step }}">2</span> Photos de l'article</h2>
        <p class="mb-4 text-sm text-on-surface-variant">
            Ajoute de 1 à 5 photos nettes et bien éclairées. La première sera la photo principale.
        </p>

        @if ($isEdit && $ad->photos->isNotEmpty())
            <div class="mb-5">
                <p class="{{ $label }}">Photos actuelles <span class="font-normal text-on-surface-variant">(coche pour supprimer)</span></p>
                <div class="mt-2 grid grid-cols-3 gap-3 sm:grid-cols-5">
                    @foreach ($ad->photos as $photo)
                        <label class="block cursor-pointer">
                            <img src="{{ $photo->url }}" alt="" class="h-24 w-full rounded-xl object-cover">
                            <span class="mt-1 flex items-center gap-1 text-xs font-semibold text-red-600">
                                <input type="checkbox" name="remove_photos[]" value="{{ $photo->id }}"
                                       class="rounded border-outline-variant text-red-600 focus:ring-red-500"> Supprimer
                            </span>
                        </label>
                    @endforeach
                </div>
            </div>
        @endif

        <div x-data="{
            files: [],
            max: 5,
            add(event) {
                const incoming = Array.from(event.target.files).filter(f => f.type.startsWith('image/'));
                this.files = [...this.files, ...incoming].slice(0, this.max);
                this.sync();
            },
            remove(index) {
                this.files.splice(index, 1);
                this.sync();
            },
            sync() {
                const dt = new DataTransfer();
                this.files.forEach(f => dt.items.add(f));
                this.$refs.input.files = dt.files;
            },
            url(file) { return URL.createObjectURL(file); }
        }">
            <label @dragover.prevent @drop.prevent="add({ target: { files: $event.dataTransfer.files } })"
                   class="flex cursor-pointer flex-col items-center gap-2 rounded-2xl border-2 border-dashed border-outline-variant bg-surface-container-low px-4 py-10 text-center transition hover:border-primary-container">
                <span class="material-symbols-outlined text-4xl text-primary-container">cloud_upload</span>
                <span class="font-semibold">Glisse tes photos ici ou <span class="text-primary underline">parcours tes fichiers</span></span>
                <span class="text-xs text-on-surface-variant">JPG, PNG ou WEBP · 2 Mo maximum par photo · 5 photos maximum</span>
                <input x-ref="input" type="file" name="photos[]" multiple accept="image/jpeg,image/png,image/webp"
                       class="sr-only" @change="add($event)" {{ $isEdit ? '' : 'required' }}>
            </label>

            <div x-show="files.length" x-cloak class="mt-4 grid grid-cols-3 gap-3 sm:grid-cols-5">
                <template x-for="(file, idx) in files" :key="file.name + file.size + idx">
                    <div class="relative">
                        <img :src="url(file)" alt="" class="h-24 w-full rounded-xl object-cover">
                        @unless ($isEdit)
                            <span x-show="idx === 0" class="absolute bottom-1 left-1 rounded bg-primary-container px-1.5 py-0.5 text-[10px] font-bold uppercase text-white">Principale</span>
                        @endunless
                        <button type="button" @click="remove(idx)" aria-label="Retirer cette photo"
                                class="absolute right-1 top-1 grid h-6 w-6 place-items-center rounded-full bg-black/60 text-white hover:bg-black/80">
                            <span class="material-symbols-outlined text-[16px]">close</span>
                        </button>
                    </div>
                </template>
            </div>
        </div>

        @error('photos') <p class="{{ $err }}">{{ $message }}</p> @enderror
        @error('photos.*') <p class="{{ $err }}">{{ $message }}</p> @enderror
    </section>

    {{-- 3. Détails et localisation --}}
    <section class="{{ $card }}">
        <h2 class="mb-5 flex items-center gap-3 text-lg font-bold"><span class="{{ $step }}">3</span> Détails et localisation</h2>

        <div>
            <label for="description" class="{{ $label }}">Description complète *</label>
            <textarea id="description" name="description" rows="7" required minlength="20" maxlength="3000"
                      placeholder="Décris ton article : marque, modèle, état, accessoires fournis, raison de la vente…"
                      class="{{ $field }}">{{ old('description', $ad->description ?? '') }}</textarea>
            <p class="mt-1 text-xs text-on-surface-variant">20 caractères minimum. Plus l'annonce est précise, plus elle reçoit de contacts.</p>
            @error('description') <p class="{{ $err }}">{{ $message }}</p> @enderror
        </div>

        <div class="mt-5">
            <label for="location" class="{{ $label }}">Commune ou ville *</label>
            <select id="location" name="location" required class="{{ $field }}">
                <option value="">Choisir…</option>
                @foreach (config('akwaba.locations') as $place)
                    <option value="{{ $place }}" @selected(old('location', $ad->location ?? null) === $place)>{{ $place }}</option>
                @endforeach
            </select>
            @error('location') <p class="{{ $err }}">{{ $message }}</p> @enderror
        </div>
    </section>

    {{-- 4. Prix et contact --}}
    <section class="{{ $card }}">
        <h2 class="mb-5 flex items-center gap-3 text-lg font-bold"><span class="{{ $step }}">4</span> Prix et contact</h2>

        <div>
            <label for="price" class="{{ $label }}">Prix de vente *</label>
            <div class="relative max-w-xs">
                <input id="price" name="price" type="number" min="0" step="1" required
                       value="{{ old('price', $ad->price ?? '') }}" placeholder="350000"
                       class="{{ $field }} pr-20 text-lg font-bold">
                <span class="pointer-events-none absolute right-4 top-1/2 -translate-y-1/2 font-bold text-primary-container">FCFA</span>
            </div>
            @error('price') <p class="{{ $err }}">{{ $message }}</p> @enderror
        </div>

        <div class="mt-5 flex items-start gap-3 rounded-xl border border-secondary-fixed-dim bg-secondary-container/30 p-4 text-sm">
            <span class="material-symbols-outlined text-secondary">call</span>
            <p class="text-on-surface-variant">
                Les acheteurs te joindront au <strong class="text-on-surface">{{ auth()->user()->phone }}</strong>
                (appel ou WhatsApp). Pour changer ce numéro, modifie ton
                <a href="{{ route('profile.edit') }}" class="font-bold text-secondary underline">profil</a>.
            </p>
        </div>
    </section>

    {{-- Actions --}}
    <div class="flex flex-col-reverse items-stretch gap-3 sm:flex-row sm:items-center sm:justify-end">
        <a href="{{ $isEdit ? route('ads.show', $ad) : route('home') }}"
           class="rounded-xl border border-outline-variant px-6 py-3 text-center font-semibold hover:bg-surface-container-low">Annuler</a>
        <button type="submit"
                class="flex items-center justify-center gap-2 rounded-xl bg-secondary px-6 py-3 font-bold text-white transition hover:opacity-90">
            <span class="material-symbols-outlined text-[20px]">{{ $isEdit ? 'save' : 'send' }}</span>
            {{ $isEdit ? 'Enregistrer les modifications' : 'Publier mon annonce gratuitement' }}
        </button>
    </div>
</form>
