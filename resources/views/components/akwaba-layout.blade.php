@props(['title' => null])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ? $title.' — ' : '' }}{{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen flex flex-col bg-ci-cream text-ci-brown font-sans antialiased">
    <div class="kita"></div>

    <header class="bg-white border-b border-orange-100">
        <div class="mx-auto flex max-w-6xl items-center justify-between gap-3 px-4 py-3">
            <a href="{{ route('home') }}" class="flex items-center gap-2 text-xl font-extrabold">
                <span class="grid h-9 w-9 place-items-center rounded-xl bg-ci-orange text-white">A</span>
                <span>Akwaba <span class="text-ci-orange">Annonces</span></span>
            </a>

            <nav class="flex items-center gap-3 text-sm font-semibold">
                <a href="{{ route('ads.create') }}" class="rounded-full bg-ci-green px-4 py-2 text-white hover:opacity-90">+ Publier</a>

                @auth
                    <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                        <button type="button" @click="open = !open" class="rounded-full border border-orange-200 px-4 py-2 hover:bg-orange-50">
                            {{ auth()->user()->login }} ▾
                        </button>
                        <div x-show="open" x-cloak class="absolute right-0 z-10 mt-2 w-48 rounded-xl border border-orange-100 bg-white py-2 shadow-lg">
                            <a href="{{ route('ads.mine') }}" class="block px-4 py-2 hover:bg-orange-50">Mes annonces</a>
                            <a href="{{ route('profile.edit') }}" class="block px-4 py-2 hover:bg-orange-50">Mon profil</a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="block w-full px-4 py-2 text-left hover:bg-orange-50">Déconnexion</button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="hover:text-ci-orange">Connexion</a>
                    <a href="{{ route('register') }}" class="hover:text-ci-orange">Inscription</a>
                @endauth
            </nav>
        </div>
    </header>

    @if (session('status'))
        <div class="mx-auto mt-4 w-full max-w-6xl px-4">
            <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-green-800">{{ session('status') }}</div>
        </div>
    @endif

    <main class="mx-auto w-full max-w-6xl flex-1 px-4 py-6">
        {{ $slot }}
    </main>

    <footer class="mt-8">
        <div class="kita"></div>
        <div class="bg-ci-brown py-6 text-center text-sm text-orange-100">
            © {{ date('Y') }} Akwaba Annonces · Petites annonces gratuites en Côte d'Ivoire
        </div>
    </footer>
</body>
</html>
