@php
    $user = auth()->user();
    $initial = mb_strtoupper(mb_substr($user->login, 0, 1));
    $card = 'rounded-2xl border border-outline-variant/50 bg-surface-container-lowest shadow-[0_2px_8px_rgba(43,30,22,0.04)]';
@endphp

<x-akwaba-layout title="Mes annonces" robots="noindex, nofollow">
    {{-- En-tête du compte --}}
    <section class="{{ $card }} p-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-4">
                <span class="grid h-16 w-16 shrink-0 place-items-center rounded-full bg-primary-container text-2xl font-extrabold text-white">{{ $initial }}</span>
                <div class="min-w-0">
                    <h1 class="truncate text-2xl font-extrabold">{{ $user->login }}</h1>
                    <p class="mt-1 flex flex-wrap items-center gap-x-4 gap-y-1 text-sm text-on-surface-variant">
                        <span class="flex items-center gap-1"><span class="material-symbols-outlined text-[16px]">call</span>{{ $user->phone }}</span>
                        <span class="flex items-center gap-1"><span class="material-symbols-outlined text-[16px]">calendar_month</span>Membre depuis {{ $user->created_at->translatedFormat('F Y') }}</span>
                    </p>
                </div>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('profile.edit') }}"
                   class="flex items-center gap-1 rounded-xl border border-outline-variant px-4 py-2.5 text-sm font-semibold hover:bg-surface-container-low">
                    <span class="material-symbols-outlined text-[18px]">edit</span> Modifier mon profil
                </a>
                <a href="{{ route('ads.create') }}"
                   class="flex items-center gap-1 rounded-xl bg-secondary px-4 py-2.5 text-sm font-bold text-white hover:opacity-90">
                    <span class="material-symbols-outlined text-[18px]">add_circle</span> Publier
                </a>
            </div>
        </div>

        <div class="mt-5 grid grid-cols-2 gap-3 border-t border-outline-variant/40 pt-5">
            <div class="flex items-center gap-3 rounded-xl bg-surface-container-low p-4">
                <span class="grid h-10 w-10 place-items-center rounded-lg bg-surface-container text-primary-container"><span class="material-symbols-outlined">inventory_2</span></span>
                <div>
                    <p class="text-xl font-extrabold leading-none">{{ $ads->total() }}</p>
                    <p class="text-xs text-on-surface-variant">Annonce{{ $ads->total() > 1 ? 's' : '' }} publiée{{ $ads->total() > 1 ? 's' : '' }}</p>
                </div>
            </div>
            <div class="flex items-center gap-3 rounded-xl bg-surface-container-low p-4">
                <span class="grid h-10 w-10 place-items-center rounded-lg bg-secondary-container/60 text-secondary"><span class="material-symbols-outlined">mark_email_read</span></span>
                <div>
                    <p class="text-xl font-extrabold leading-none">{{ $user->hasVerifiedEmail() ? 'Vérifié' : 'Non vérifié' }}</p>
                    <p class="text-xs text-on-surface-variant">Adresse email</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Liste --}}
    <h2 class="mb-4 mt-8 text-xl font-bold">Mes annonces</h2>

    <div class="space-y-4">
        @forelse ($ads as $ad)
            <article class="{{ $card }} flex flex-col gap-4 p-4 sm:flex-row">
                <a href="{{ route('ads.show', $ad) }}" class="relative block h-44 shrink-0 overflow-hidden rounded-xl bg-surface-container sm:h-32 sm:w-44">
                    @if ($ad->cover_url)
                        <img src="{{ $ad->cover_url }}" alt="{{ $ad->title }}" loading="lazy" class="h-full w-full object-cover">
                    @else
                        <span class="flex h-full items-center justify-center"><span class="material-symbols-outlined text-4xl text-outline">image</span></span>
                    @endif
                    <span class="absolute bottom-2 right-2 flex items-center gap-1 rounded-full bg-inverse-surface/80 px-2 py-0.5 text-xs font-semibold text-white">
                        <span class="material-symbols-outlined text-[14px]">photo_camera</span>{{ $ad->photos->count() }}
                    </span>
                </a>

                <div class="min-w-0 flex-1">
                    <div class="flex flex-wrap items-center gap-x-2 gap-y-1 text-xs font-bold uppercase tracking-wide">
                        <span class="text-primary">{{ $ad->category->name }}</span>
                        <span class="text-outline">•</span>
                        <span class="flex items-center gap-1 normal-case text-on-surface-variant">
                            <span class="material-symbols-outlined text-[14px]">location_on</span>{{ $ad->location }}
                        </span>
                        <span class="text-outline">•</span>
                        <span class="font-medium normal-case text-on-surface-variant">Publiée {{ $ad->created_at->diffForHumans() }}</span>
                    </div>
                    <a href="{{ route('ads.show', $ad) }}" class="mt-1 block truncate text-lg font-bold hover:text-primary-container">{{ $ad->title }}</a>
                    <p class="mt-1 text-xl font-extrabold text-primary-container">{{ $ad->price_formatted }}</p>
                    <p class="mt-1 text-sm text-on-surface-variant">{{ $ad->condition_label }}</p>
                </div>

                <div class="grid grid-cols-3 gap-2 text-sm font-bold sm:w-32 sm:grid-cols-1">
                    <a href="{{ route('ads.show', $ad) }}"
                       class="flex items-center justify-center gap-1 rounded-xl border border-outline-variant px-3 py-2 hover:bg-surface-container-low">
                        <span class="material-symbols-outlined text-[18px]">visibility</span> Voir
                    </a>
                    <a href="{{ route('ads.edit', $ad) }}"
                       class="flex items-center justify-center gap-1 rounded-xl border border-primary-container px-3 py-2 text-primary hover:bg-surface-container-low">
                        <span class="material-symbols-outlined text-[18px]">edit</span> Modifier
                    </a>
                    <form method="POST" action="{{ route('ads.destroy', $ad) }}" onsubmit="return confirm('Supprimer définitivement cette annonce ?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="flex w-full items-center justify-center gap-1 rounded-xl border border-red-300 px-3 py-2 text-red-600 hover:bg-red-50">
                            <span class="material-symbols-outlined text-[18px]">delete</span> Suppr.
                        </button>
                    </form>
                </div>
            </article>
        @empty
            <div class="{{ $card }} p-10 text-center">
                <span class="material-symbols-outlined text-5xl text-outline">inventory_2</span>
                <p class="mt-2 font-bold">Tu n'as pas encore publié d'annonce.</p>
                <a href="{{ route('ads.create') }}" class="mt-3 inline-flex items-center gap-1 rounded-xl bg-secondary px-5 py-2.5 font-bold text-white hover:opacity-90">
                    <span class="material-symbols-outlined text-[18px]">add_circle</span> Publier ma première annonce
                </a>
            </div>
        @endforelse
    </div>

    <div class="mt-8">{{ $ads->links() }}</div>
</x-akwaba-layout>
