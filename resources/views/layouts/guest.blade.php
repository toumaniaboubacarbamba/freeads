<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }}</title>
    <meta name="robots" content="noindex, follow">
    <meta name="theme-color" content="#f77f00">
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-screen flex-col bg-surface font-sans text-on-surface antialiased">
    <div class="kita-strip"></div>

    <header class="border-b border-outline-variant/40 bg-surface-container-lowest">
        <div class="mx-auto flex max-w-6xl items-center justify-between px-4 py-3 sm:px-6">
            <a href="{{ route('home') }}" class="flex items-center gap-2">
                <span class="grid h-10 w-10 place-items-center rounded-xl bg-primary-container text-white">
                    <span class="material-symbols-outlined">storefront</span>
                </span>
                <span class="text-lg font-extrabold text-primary-container">Akwaba Annonces</span>
            </a>
            <a href="{{ route('home') }}" class="flex items-center gap-1 rounded-xl border border-outline-variant px-3 py-2 text-sm font-semibold hover:bg-surface-container-low">
                <span class="material-symbols-outlined text-[18px]">arrow_back</span> Retour au site
            </a>
        </div>
    </header>

    <main class="flex flex-1 items-center justify-center px-4 py-8 sm:px-6 sm:py-12">
        <div class="grid w-full max-w-5xl overflow-hidden rounded-3xl border border-outline-variant/50 bg-surface-container-lowest shadow-[0_16px_36px_rgba(43,30,22,0.10)] lg:grid-cols-2">

            {{-- Panneau de gauche --}}
            <div class="kita-soft hidden flex-col justify-center gap-6 bg-surface-container-low p-10 lg:flex">
                <span class="w-fit rounded-full border border-outline-variant bg-white px-3 py-1 text-xs font-bold uppercase tracking-wide text-secondary">
                    Communauté ivoirienne
                </span>
                <h2 class="text-4xl font-extrabold leading-tight tracking-tight">
                    Bienvenue sur <span class="text-primary-container">Akwaba Annonces</span> !
                </h2>
                <p class="text-on-surface-variant">
                    Achète et vends près de chez toi, de Cocody à San-Pédro, en toute simplicité.
                </p>
                <ul class="space-y-4 text-sm">
                    <li class="flex gap-3">
                        <span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-white text-primary-container"><span class="material-symbols-outlined">bolt</span></span>
                        <span><strong class="block">Publie en quelques minutes</strong><span class="text-on-surface-variant">Un titre, des photos, un prix : ton annonce est en ligne tout de suite.</span></span>
                    </li>
                    <li class="flex gap-3">
                        <span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-white text-secondary"><span class="material-symbols-outlined">chat</span></span>
                        <span><strong class="block">Contact direct</strong><span class="text-on-surface-variant">Les acheteurs t'appellent ou t'écrivent sur WhatsApp.</span></span>
                    </li>
                    <li class="flex gap-3">
                        <span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-white text-primary-container"><span class="material-symbols-outlined">sell</span></span>
                        <span><strong class="block">100 % gratuit</strong><span class="text-on-surface-variant">Aucune commission, aucun frais de publication.</span></span>
                    </li>
                </ul>
            </div>

            {{-- Formulaire --}}
            <div class="p-6 sm:p-10">
                {{ $slot }}
            </div>
        </div>
    </main>

    <footer class="border-t border-outline-variant/40 py-4 text-center text-xs text-on-surface-variant">
        © {{ date('Y') }} Akwaba Annonces · Projet Freeads (Coding Academy)
    </footer>
</body>
</html>
