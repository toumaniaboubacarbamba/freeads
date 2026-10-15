@php
    // Une page filtrée ne doit pas être indexée (contenu dupliqué) : seule la liste de base l'est.
    $isFiltered = collect(request()->query())->except('page')->filter(fn ($v) => filled($v) && $v !== 'latest')->isNotEmpty();
    $canonical = $isFiltered
        ? route('home')
        : route('home', $ads->currentPage() > 1 ? ['page' => $ads->currentPage()] : []);
    $selectedCategories = collect(request('categories', []))->map(fn ($id) => (int) $id)->all();
    $currentCondition = request('condition', '');
    $currentSort = request('sort', 'latest');
    $field = 'w-full rounded-xl border-[1.5px] border-outline-variant bg-white px-4 py-3 text-sm focus:border-primary-container focus:ring-4 focus:ring-primary-container/15';
@endphp

<x-akwaba-layout title="Petites annonces gratuites en Côte d'Ivoire"
                 description="Achetez et vendez près de chez vous : véhicules, immobilier, téléphones, emploi et plus encore. Annonces gratuites à Abidjan, Bouaké, Yamoussoukro et partout en Côte d'Ivoire."
                 :canonical="$canonical"
                 :robots="$isFiltered ? 'noindex, follow' : null">
    <form method="GET" action="{{ route('home') }}">

        {{-- Bandeau + barre de recherche --}}
        <section class="kita-soft rounded-3xl border border-outline-variant/40 bg-surface-container-low px-4 py-10 text-center sm:px-8">
            <h1 class="text-3xl font-extrabold tracking-tight sm:text-4xl">
                Trouvez la bonne affaire partout en <span class="text-primary-container">Côte d'Ivoire</span>
            </h1>
            <p class="mx-auto mt-3 max-w-2xl text-on-surface-variant">
                Des annonces à Abidjan, Bouaké, Yamoussoukro et dans toutes les communes.
            </p>

            <div class="mx-auto mt-6 flex max-w-4xl flex-col gap-2 rounded-2xl bg-white p-2 shadow-[0_8px_20px_rgba(43,30,22,0.08)] md:flex-row md:items-center">
                <label class="flex flex-1 items-center gap-2 px-3">
                    <span class="material-symbols-outlined text-outline">search</span>
                    <input type="search" name="q" value="{{ request('q') }}" maxlength="100"
                           placeholder="Que recherchez-vous ? (ex : iPhone, terrain Bingerville)"
                           class="w-full border-0 bg-transparent py-3 text-sm focus:ring-0">
                </label>

                <label class="flex items-center gap-2 border-outline-variant px-3 md:border-l">
                    <span class="material-symbols-outlined text-secondary">location_on</span>
                    <select name="location" class="border-0 bg-transparent py-3 pr-8 text-sm focus:ring-0">
                        <option value="">Toute la Côte d'Ivoire</option>
                        @foreach (config('akwaba.locations') as $place)
                            <option value="{{ $place }}" @selected(request('location') === $place)>{{ $place }}</option>
                        @endforeach
                    </select>
                </label>

                <button type="submit"
                        class="flex items-center justify-center gap-2 rounded-xl bg-primary-container px-6 py-3 font-bold text-white transition hover:bg-[#d86b00]">
                    <span class="material-symbols-outlined text-[20px]">search</span> Chercher
                </button>
            </div>

            <div class="mt-5 flex flex-wrap items-center justify-center gap-2 text-sm">
                <span class="font-semibold text-on-surface-variant">Populaire :</span>
                @foreach ($categories->take(5) as $category)
                    <a href="{{ route('home', ['categories' => [$category->id]]) }}"
                       class="rounded-full border border-outline-variant bg-white px-3 py-1 font-semibold hover:border-primary-container hover:text-primary-container">
                        {{ $category->name }}
                    </a>
                @endforeach
            </div>
        </section>

        <div class="mt-8 grid gap-8 lg:grid-cols-[17rem_1fr]">

            {{-- Filtres --}}
            <aside x-data="{ open: window.innerWidth >= 1024 }" class="lg:sticky lg:top-24 lg:self-start">
                <button type="button" @click="open = !open"
                        class="mb-3 flex w-full items-center justify-between rounded-xl border border-outline-variant bg-white px-4 py-3 font-bold lg:hidden">
                    <span class="flex items-center gap-2"><span class="material-symbols-outlined">tune</span> Filtres</span>
                    <span class="material-symbols-outlined" x-text="open ? 'expand_less' : 'expand_more'">expand_more</span>
                </button>

                <div x-show="open" class="space-y-6 rounded-2xl border border-outline-variant/50 bg-white p-5 shadow-[0_2px_8px_rgba(43,30,22,0.04)]">
                    <div class="flex items-center justify-between">
                        <p class="flex items-center gap-2 text-lg font-bold"><span class="material-symbols-outlined">tune</span> Filtres</p>
                        <a href="{{ route('home') }}" class="text-xs font-bold text-primary hover:underline">Réinitialiser</a>
                    </div>

                    <fieldset>
                        <legend class="mb-2 text-sm font-bold">Catégories</legend>
                        <div class="space-y-2">
                            @foreach ($categories as $category)
                                <label class="flex items-center justify-between gap-2 text-sm">
                                    <span class="flex items-center gap-2">
                                        <input type="checkbox" name="categories[]" value="{{ $category->id }}"
                                               @checked(in_array($category->id, $selectedCategories))
                                               class="rounded border-outline-variant text-primary-container focus:ring-primary-container">
                                        {{ $category->name }}
                                    </span>
                                    <span class="text-xs text-on-surface-variant">{{ $category->ads_count }}</span>
                                </label>
                            @endforeach
                        </div>
                    </fieldset>

                    <fieldset class="border-t border-outline-variant/40 pt-5">
                        <legend class="mb-2 text-sm font-bold">Prix (FCFA)</legend>
                        <div class="grid grid-cols-2 gap-2">
                            <input type="number" name="price_min" min="0" value="{{ request('price_min') }}" placeholder="Min" class="{{ $field }}">
                            <input type="number" name="price_max" min="0" value="{{ request('price_max') }}" placeholder="Max" class="{{ $field }}">
                        </div>
                    </fieldset>

                    <fieldset class="border-t border-outline-variant/40 pt-5">
                        <legend class="mb-2 text-sm font-bold">État</legend>
                        <div class="space-y-2 text-sm">
                            <label class="flex items-center gap-2">
                                <input type="radio" name="condition" value="" @checked($currentCondition === '')
                                       class="border-outline-variant text-primary-container focus:ring-primary-container"> Tous
                            </label>
                            @foreach (config('akwaba.conditions') as $key => $label)
                                <label class="flex items-center gap-2">
                                    <input type="radio" name="condition" value="{{ $key }}" @checked($currentCondition === $key)
                                           class="border-outline-variant text-primary-container focus:ring-primary-container"> {{ $label }}
                                </label>
                            @endforeach
                        </div>
                    </fieldset>

                    <button type="submit"
                            class="flex w-full items-center justify-center gap-2 rounded-xl bg-secondary px-4 py-3 font-bold text-white hover:opacity-90">
                        <span class="material-symbols-outlined text-[20px]">check_circle</span> Appliquer les filtres
                    </button>
                </div>
            </aside>

            {{-- Résultats --}}
            <section>
                <div class="mb-5 flex flex-wrap items-end justify-between gap-3">
                    <div>
                        <h2 class="text-2xl font-bold">Annonces</h2>
                        <p class="text-sm text-on-surface-variant">
                            @if ($ads->total())
                                Affichage de {{ $ads->firstItem() }} à {{ $ads->lastItem() }} sur {{ $ads->total() }} annonce{{ $ads->total() > 1 ? 's' : '' }}
                            @else
                                Aucun résultat
                            @endif
                        </p>
                    </div>

                    <label class="flex items-center gap-2 text-sm">
                        <span class="text-on-surface-variant">Trier par :</span>
                        <select name="sort" onchange="this.form.submit()"
                                class="rounded-xl border-outline-variant bg-white py-2 pr-8 text-sm focus:border-primary-container focus:ring-primary-container">
                            <option value="latest" @selected($currentSort === 'latest')>Plus récentes</option>
                            <option value="price_asc" @selected($currentSort === 'price_asc')>Prix croissant</option>
                            <option value="price_desc" @selected($currentSort === 'price_desc')>Prix décroissant</option>
                        </select>
                    </label>
                </div>

                @if ($ads->isEmpty())
                    <div class="rounded-2xl border border-outline-variant/50 bg-white p-10 text-center">
                        <span class="material-symbols-outlined text-5xl text-outline">search_off</span>
                        <p class="mt-2 font-bold">Aucune annonce ne correspond à ta recherche.</p>
                        <p class="mt-1 text-sm text-on-surface-variant">
                            Essaie d'élargir les filtres, ou
                            <a href="{{ route('ads.create') }}" class="font-bold text-secondary hover:underline">publie la première annonce</a>.
                        </p>
                    </div>
                @else
                    <div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-3">
                        @foreach ($ads as $ad)
                            @include('ads._card')
                        @endforeach
                    </div>
                @endif

                <div class="mt-8">{{ $ads->links() }}</div>
            </section>
        </div>
    </form>
</x-akwaba-layout>
