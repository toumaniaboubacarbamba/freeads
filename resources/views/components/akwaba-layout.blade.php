@props(['title' => null])
@php
    $cats = $footerCategories ?? collect();
    $navCats = $cats->whereIn('slug', ['vehicules', 'immobilier', 'telephones']);
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ? $title.' — ' : '' }}{{ config('app.name') }}</title>
    <meta name="description" content="Petites annonces gratuites en Côte d'Ivoire : véhicules, immobilier, téléphones, emploi et plus encore.">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-screen flex-col bg-surface font-sans text-on-surface antialiased">
    <div class="kita-strip"></div>

    {{-- En-tête --}}
    <header class="sticky top-0 z-30 border-b border-outline-variant/40 bg-surface-container-lowest/95 backdrop-blur">
        <div class="mx-auto flex max-w-7xl items-center justify-between gap-3 px-4 py-3 sm:px-6 lg:px-10">
            <a href="{{ route('home') }}" class="flex items-center gap-2">
                <span class="grid h-10 w-10 place-items-center rounded-xl bg-primary-container text-white">
                    <span class="material-symbols-outlined">storefront</span>
                </span>
                <span class="leading-tight">
                    <span class="block text-lg font-extrabold text-primary-container">Akwaba Annonces</span>
                    <span class="block text-[10px] font-bold uppercase tracking-wider text-secondary">Le carrefour ivoirien</span>
                </span>
            </a>

            <nav class="hidden items-center gap-6 text-sm font-semibold lg:flex">
                @foreach ($navCats as $navCat)
                    <a href="{{ route('home', ['categories' => [$navCat->id]]) }}" class="hover:text-primary-container">{{ $navCat->name }}</a>
                @endforeach
            </nav>

            <div class="flex items-center gap-2 text-sm font-semibold sm:gap-3">
                @auth
                    <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                        <button type="button" @click="open = !open"
                                class="flex items-center gap-2 rounded-full border border-outline-variant bg-surface-container-low px-3 py-2 hover:bg-surface-container">
                            <span class="material-symbols-outlined text-[20px]">account_circle</span>
                            <span class="hidden max-w-[8rem] truncate sm:inline">{{ auth()->user()->login }}</span>
                            <span class="material-symbols-outlined text-[18px]">expand_more</span>
                        </button>
                        <div x-show="open" x-cloak
                             class="absolute right-0 z-40 mt-2 w-52 rounded-xl border border-outline-variant/60 bg-white py-2 shadow-[0_8px_20px_rgba(43,30,22,0.08)]">
                            <a href="{{ route('ads.mine') }}" class="flex items-center gap-2 px-4 py-2 hover:bg-surface-container-low">
                                <span class="material-symbols-outlined text-[20px]">inventory_2</span> Mes annonces
                            </a>
                            <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 px-4 py-2 hover:bg-surface-container-low">
                                <span class="material-symbols-outlined text-[20px]">person</span> Mon profil
                            </a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="flex w-full items-center gap-2 px-4 py-2 text-left hover:bg-surface-container-low">
                                    <span class="material-symbols-outlined text-[20px]">logout</span> Déconnexion
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="hidden px-2 hover:text-primary-container sm:inline">Connexion</a>
                    <a href="{{ route('register') }}" class="hidden px-2 hover:text-primary-container sm:inline">Inscription</a>
                @endauth

                <a href="{{ route('ads.create') }}"
                   class="inline-flex items-center gap-1 rounded-xl bg-secondary px-4 py-2.5 font-bold text-white transition hover:opacity-90">
                    <span class="material-symbols-outlined text-[20px]">add_circle</span>
                    <span class="hidden sm:inline">Publier une annonce</span>
                    <span class="sm:hidden">Publier</span>
                </a>
            </div>
        </div>
    </header>

    @php
        $flash = session('status');
        $flash = [
            'profile-updated' => 'Ton profil a été mis à jour.',
            'password-updated' => 'Ton mot de passe a été modifié.',
            'verification-link-sent' => 'Un nouveau lien de confirmation a été envoyé.',
        ][$flash] ?? $flash;
    @endphp

    @if ($flash)
        <div class="mx-auto mt-4 w-full max-w-7xl px-4 sm:px-6 lg:px-10">
            <div class="flex items-center gap-2 rounded-xl border border-secondary-fixed-dim bg-secondary-container/40 px-4 py-3 text-on-secondary-container">
                <span class="material-symbols-outlined">check_circle</span> {{ $flash }}
            </div>
        </div>
    @endif

    <main class="mx-auto w-full max-w-7xl flex-1 px-4 py-6 sm:px-6 lg:px-10">
        {{ $slot }}
    </main>

    {{-- Pied de page --}}
    <footer class="mt-10 bg-surface-container-lowest">
        <div class="kita-strip"></div>
        <div class="mx-auto grid max-w-7xl gap-8 px-4 py-10 sm:px-6 md:grid-cols-4 lg:px-10">
            <div>
                <p class="text-lg font-extrabold text-primary-container">Akwaba Annonces</p>
                <p class="mt-2 text-sm text-on-surface-variant">
                    Petites annonces gratuites en Côte d'Ivoire. Achetez, vendez et échangez d'Abidjan à Man.
                </p>
            </div>

            <div>
                <p class="mb-3 text-sm font-bold uppercase tracking-wide">Catégories</p>
                <ul class="space-y-2 text-sm text-on-surface-variant">
                    @foreach ($cats as $cat)
                        <li><a href="{{ route('home', ['categories' => [$cat->id]]) }}" class="hover:text-primary-container">{{ $cat->name }}</a></li>
                    @endforeach
                </ul>
            </div>

            <div>
                <p class="mb-3 text-sm font-bold uppercase tracking-wide">Villes</p>
                <ul class="space-y-2 text-sm text-on-surface-variant">
                    @foreach (['Cocody', 'Yopougon', 'Bouaké', 'Yamoussoukro', 'San-Pédro'] as $city)
                        <li><a href="{{ route('home', ['location' => $city]) }}" class="hover:text-primary-container">{{ $city }}</a></li>
                    @endforeach
                </ul>
            </div>

            <div>
                <p class="mb-3 text-sm font-bold uppercase tracking-wide">Compte</p>
                <ul class="space-y-2 text-sm text-on-surface-variant">
                    <li><a href="{{ route('ads.create') }}" class="hover:text-primary-container">Publier une annonce</a></li>
                    @auth
                        <li><a href="{{ route('ads.mine') }}" class="hover:text-primary-container">Mes annonces</a></li>
                        <li><a href="{{ route('profile.edit') }}" class="hover:text-primary-container">Mon profil</a></li>
                    @else
                        <li><a href="{{ route('login') }}" class="hover:text-primary-container">Connexion</a></li>
                        <li><a href="{{ route('register') }}" class="hover:text-primary-container">Inscription</a></li>
                    @endauth
                </ul>
            </div>
        </div>
        <div class="border-t border-outline-variant/40 py-4 text-center text-xs text-on-surface-variant">
            © {{ date('Y') }} Akwaba Annonces · Projet Freeads (Coding Academy)
        </div>
    </footer>
</body>
</html>
