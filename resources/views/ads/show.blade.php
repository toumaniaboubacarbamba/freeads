@php
    $photoUrls = $ad->photos->pluck('url')->values();
    $badge = [
        'new'  => 'bg-secondary',
        'good' => 'bg-primary-container',
        'used' => 'bg-inverse-surface/80',
    ][$ad->condition] ?? 'bg-inverse-surface/80';
    $initial = mb_strtoupper(mb_substr($ad->user->login, 0, 1));
    $waText = 'Bonjour, je suis intéressé(e) par votre annonce « '.$ad->title.' » sur Akwaba Annonces.';
    $conditionSchema = ['new' => 'NewCondition', 'good' => 'UsedCondition', 'used' => 'UsedCondition'][$ad->condition] ?? 'UsedCondition';
    $plainDescription = trim(preg_replace('/\s+/', ' ', strip_tags($ad->description)));
    $metaDescription = $ad->price_formatted.' — '.\Illuminate\Support\Str::limit($plainDescription, 140);
    $canonical = route('ads.show', $ad);
    $jsonLd = [
        '@context' => 'https://schema.org',
        '@type' => 'Product',
        'name' => $ad->title,
        'description' => $plainDescription,
        'image' => $photoUrls->all(),
        'category' => $ad->category->name,
        'itemCondition' => 'https://schema.org/'.$conditionSchema,
        'offers' => [
            '@type' => 'Offer',
            'url' => $canonical,
            'price' => (string) $ad->price,
            'priceCurrency' => 'XOF',
            'availability' => 'https://schema.org/InStock',
            'areaServed' => $ad->location.', Côte d\'Ivoire',
            'seller' => ['@type' => 'Person', 'name' => $ad->user->login],
        ],
    ];
    $card = 'rounded-2xl border border-outline-variant/50 bg-surface-container-lowest p-6 shadow-[0_2px_8px_rgba(43,30,22,0.04)]';
@endphp

<x-akwaba-layout :title="$ad->title.' à '.$ad->location"
                 :description="$metaDescription"
                 :image="$photoUrls->first()"
                 :canonical="$canonical"
                 type="product">
    <x-slot:head>
        {{-- Données structurées : permet à Google d'afficher le prix dans les résultats --}}
        <script type="application/ld+json">{!! json_encode($jsonLd, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP) !!}</script>
    </x-slot:head>

    {{-- Fil d'Ariane --}}
    <nav class="mb-4 flex flex-wrap items-center gap-1 text-sm text-on-surface-variant" aria-label="Fil d'Ariane">
        <a href="{{ route('home') }}" class="hover:text-primary-container">Accueil</a>
        <span class="material-symbols-outlined text-[16px]">chevron_right</span>
        <a href="{{ route('home', ['categories' => [$ad->category_id]]) }}" class="hover:text-primary-container">{{ $ad->category->name }}</a>
        <span class="material-symbols-outlined text-[16px]">chevron_right</span>
        <span class="max-w-[14rem] truncate font-semibold text-on-surface sm:max-w-md">{{ $ad->title }}</span>
        <span class="ml-auto flex items-center gap-1">
            <span class="material-symbols-outlined text-[16px]">schedule</span> Publiée {{ $ad->created_at->diffForHumans() }}
        </span>
    </nav>

    <div class="grid gap-6 lg:grid-cols-3">

        {{-- Colonne principale --}}
        <div class="space-y-6 lg:col-span-2">

            {{-- Galerie --}}
            <div class="{{ $card }} !p-3" x-data="{ photos: @js($photoUrls), i: 0 }">
                <div class="relative aspect-[4/3] overflow-hidden rounded-xl bg-surface-container sm:aspect-[16/10]">
                    <template x-if="photos.length">
                        <img :src="photos[i]" alt="{{ $ad->title }}" class="h-full w-full object-cover">
                    </template>
                    @if ($photoUrls->isEmpty())
                        <div class="flex h-full items-center justify-center">
                            <span class="material-symbols-outlined text-6xl text-outline">image</span>
                        </div>
                    @endif

                    <div class="absolute left-3 top-3 flex flex-wrap gap-2 text-xs font-bold">
                        <span class="rounded-full px-3 py-1 text-white {{ $badge }}">{{ $ad->condition_label }}</span>
                        <span class="flex items-center gap-1 rounded-full bg-white/90 px-3 py-1">
                            <span class="material-symbols-outlined text-[14px] text-secondary">location_on</span>{{ $ad->location }}
                        </span>
                    </div>

                    <button type="button" x-show="photos.length > 1" @click="i = (i - 1 + photos.length) % photos.length"
                            class="absolute left-3 top-1/2 grid h-10 w-10 -translate-y-1/2 place-items-center rounded-full bg-white/90 shadow hover:bg-white" aria-label="Photo précédente">
                        <span class="material-symbols-outlined">chevron_left</span>
                    </button>
                    <button type="button" x-show="photos.length > 1" @click="i = (i + 1) % photos.length"
                            class="absolute right-3 top-1/2 grid h-10 w-10 -translate-y-1/2 place-items-center rounded-full bg-white/90 shadow hover:bg-white" aria-label="Photo suivante">
                        <span class="material-symbols-outlined">chevron_right</span>
                    </button>

                    <span class="absolute bottom-3 right-3 flex items-center gap-1 rounded-full bg-inverse-surface/80 px-3 py-1 text-xs font-semibold text-white">
                        <span class="material-symbols-outlined text-[14px]">photo_camera</span>
                        <span x-text="(i + 1) + ' / ' + photos.length"></span>
                    </span>
                </div>

                <div x-show="photos.length > 1" class="mt-3 flex gap-2 overflow-x-auto pb-1">
                    <template x-for="(url, idx) in photos" :key="idx">
                        <button type="button" @click="i = idx"
                                :class="i === idx ? 'border-primary-container' : 'border-transparent opacity-80'"
                                class="shrink-0 overflow-hidden rounded-xl border-2 transition">
                            <img :src="url" alt="" class="h-20 w-24 object-cover">
                        </button>
                    </template>
                </div>
            </div>

            {{-- Titre, prix, infos --}}
            <div class="{{ $card }}">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                    <div class="min-w-0">
                        <div class="flex flex-wrap items-center gap-2 text-xs font-bold uppercase tracking-wide">
                            <span class="rounded-md bg-surface-container px-2 py-1 text-primary">{{ $ad->category->name }}</span>
                            <span class="text-on-surface-variant">Réf. #AKW-{{ str_pad($ad->id, 5, '0', STR_PAD_LEFT) }}</span>
                        </div>
                        <h1 class="mt-3 text-2xl font-extrabold leading-tight sm:text-3xl">{{ $ad->title }}</h1>
                        <p class="mt-2 flex items-center gap-1 text-sm text-on-surface-variant">
                            <span class="material-symbols-outlined text-[18px] text-secondary">location_on</span>{{ $ad->location }}, Côte d'Ivoire
                        </p>
                    </div>

                    <div class="shrink-0 rounded-2xl border border-primary-container/30 bg-surface-container px-5 py-3 sm:text-right">
                        <p class="text-[10px] font-bold uppercase tracking-wider text-on-surface-variant">Prix demandé</p>
                        <p class="text-2xl font-extrabold text-primary-container sm:text-3xl">{{ $ad->price_formatted }}</p>
                    </div>
                </div>
            </div>

            {{-- Description --}}
            <div class="{{ $card }}">
                <h2 class="mb-3 flex items-center gap-2 text-lg font-bold">
                    <span class="material-symbols-outlined text-primary-container">description</span> Description de l'annonce
                </h2>
                <p class="leading-relaxed text-on-surface">{!! nl2br(e($ad->description)) !!}</p>
            </div>

            {{-- Conseils de sécurité --}}
            <div class="rounded-2xl border border-secondary-fixed-dim bg-secondary-container/30 p-6">
                <h2 class="mb-2 flex items-center gap-2 text-lg font-bold text-secondary">
                    <span class="material-symbols-outlined">verified_user</span> Conseils de sécurité
                </h2>
                <ul class="list-disc space-y-1 pl-5 text-sm text-on-surface-variant">
                    <li>Rencontrez toujours le vendeur dans un lieu public fréquenté.</li>
                    <li>Ne versez jamais d'acompte avant d'avoir vu et essayé l'article.</li>
                    <li>Méfiez-vous des prix anormalement bas et des demandes de paiement à l'avance.</li>
                </ul>
            </div>
        </div>

        {{-- Colonne vendeur --}}
        <aside class="space-y-6">
            <div class="{{ $card }}">
                <div class="flex items-center gap-3">
                    <span class="grid h-14 w-14 place-items-center rounded-full bg-primary-container text-xl font-extrabold text-white">{{ $initial }}</span>
                    <div class="min-w-0">
                        <p class="truncate text-lg font-bold">{{ $ad->user->login }}</p>
                        <p class="text-xs text-on-surface-variant">Membre depuis {{ $ad->user->created_at->translatedFormat('F Y') }}</p>
                    </div>
                </div>

                <div class="mt-5 grid gap-2">
                    <a href="https://wa.me/{{ $ad->user->whatsapp_number }}?text={{ urlencode($waText) }}" target="_blank" rel="noopener"
                       class="flex items-center justify-center gap-2 rounded-xl bg-secondary px-4 py-3 font-bold text-white transition hover:opacity-90">
                        <span class="material-symbols-outlined text-[20px]">chat</span> Discuter sur WhatsApp
                    </a>
                    <a href="tel:{{ $ad->user->phone }}"
                       class="flex items-center justify-center gap-2 rounded-xl bg-primary-container px-4 py-3 font-bold text-white transition hover:bg-[#d86b00]">
                        <span class="material-symbols-outlined text-[20px]">call</span> Appeler le vendeur
                    </a>
                    <button type="button" x-data="{ copied: false }"
                            @click="navigator.clipboard.writeText(window.location.href); copied = true; setTimeout(() => copied = false, 2000)"
                            class="flex items-center justify-center gap-2 rounded-xl border border-outline-variant px-4 py-3 font-semibold transition hover:bg-surface-container-low">
                        <span class="material-symbols-outlined text-[20px]" x-text="copied ? 'check' : 'share'">share</span>
                        <span x-text="copied ? 'Lien copié !' : 'Partager l\'annonce'">Partager l'annonce</span>
                    </button>
                </div>
            </div>

            @can('update', $ad)
                <div class="{{ $card }}">
                    <p class="mb-3 text-sm font-bold">Gérer mon annonce</p>
                    <div class="grid grid-cols-2 gap-2">
                        <a href="{{ route('ads.edit', $ad) }}"
                           class="flex items-center justify-center gap-1 rounded-xl border border-primary-container px-3 py-2 text-sm font-bold text-primary hover:bg-surface-container-low">
                            <span class="material-symbols-outlined text-[18px]">edit</span> Modifier
                        </a>
                        <form method="POST" action="{{ route('ads.destroy', $ad) }}" onsubmit="return confirm('Supprimer définitivement cette annonce ?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="flex w-full items-center justify-center gap-1 rounded-xl border border-red-300 px-3 py-2 text-sm font-bold text-red-600 hover:bg-red-50">
                                <span class="material-symbols-outlined text-[18px]">delete</span> Supprimer
                            </button>
                        </form>
                    </div>
                </div>
            @endcan

            @if ($sellerAds->isNotEmpty())
                <div class="{{ $card }}">
                    <p class="mb-3 font-bold">Du même vendeur</p>
                    <div class="space-y-3">
                        @foreach ($sellerAds as $other)
                            <a href="{{ route('ads.show', $other) }}" class="flex items-center gap-3 rounded-xl p-1 transition hover:bg-surface-container-low">
                                <span class="h-16 w-16 shrink-0 overflow-hidden rounded-lg bg-surface-container">
                                    @if ($other->cover_url)
                                        <img src="{{ $other->cover_url }}" alt="" class="h-full w-full object-cover">
                                    @endif
                                </span>
                                <span class="min-w-0">
                                    <span class="block truncate text-sm font-semibold">{{ $other->title }}</span>
                                    <span class="text-sm font-extrabold text-primary-container">{{ $other->price_formatted }}</span>
                                </span>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </aside>
    </div>
    {{-- Espace réservé sous la barre de contact mobile --}}
    <div class="h-20 lg:hidden"></div>

    {{-- Barre de contact collée en bas sur mobile --}}
    <div class="fixed inset-x-0 bottom-0 z-30 border-t border-outline-variant/50 bg-white/95 p-3 backdrop-blur lg:hidden"
         style="padding-bottom: max(0.75rem, env(safe-area-inset-bottom))">
        <div class="mx-auto grid max-w-md grid-cols-2 gap-2">
            <a href="https://wa.me/{{ $ad->user->whatsapp_number }}?text={{ urlencode($waText) }}" target="_blank" rel="noopener"
               class="flex items-center justify-center gap-2 rounded-xl bg-secondary px-4 py-3 font-bold text-white">
                <span class="material-symbols-outlined text-[20px]">chat</span> WhatsApp
            </a>
            <a href="tel:{{ $ad->user->phone }}"
               class="flex items-center justify-center gap-2 rounded-xl bg-primary-container px-4 py-3 font-bold text-white">
                <span class="material-symbols-outlined text-[20px]">call</span> Appeler
            </a>
        </div>
    </div>
</x-akwaba-layout>
