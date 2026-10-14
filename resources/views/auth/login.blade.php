<x-guest-layout>
    {{-- Onglets --}}
    <div class="mb-6 grid grid-cols-2 border-b border-outline-variant text-center font-bold">
        <span class="border-b-2 border-primary-container pb-3 text-primary-container">Se connecter</span>
        <a href="{{ route('register') }}" class="pb-3 text-on-surface-variant hover:text-primary-container">Créer un compte</a>
    </div>

    @if (session('status'))
        <div class="mb-4 rounded-xl border border-secondary-fixed-dim bg-secondary-container/40 px-4 py-3 text-sm text-on-secondary-container">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <x-auth-field name="email" type="email" label="Adresse email" icon="mail"
                      autocomplete="username" placeholder="ex : awa@gmail.com" :autofocus="true" />

        <x-password-field name="password" label="Mot de passe" autocomplete="current-password" />

        <div class="flex items-center justify-between text-sm">
            <label class="flex items-center gap-2">
                <input type="checkbox" name="remember" class="rounded border-outline-variant text-primary-container focus:ring-primary-container">
                Rester connecté
            </label>
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="font-bold text-primary hover:underline">Mot de passe oublié ?</a>
            @endif
        </div>

        <button type="submit"
                class="flex w-full items-center justify-center gap-2 rounded-xl bg-primary-container px-4 py-3 font-bold text-white transition hover:bg-[#d86b00]">
            <span class="material-symbols-outlined text-[20px]">login</span> Me connecter
        </button>
    </form>

    <p class="mt-6 text-center text-sm text-on-surface-variant">
        Pas encore de compte ?
        <a href="{{ route('register') }}" class="font-bold text-primary hover:underline">Créer un compte</a>
    </p>
</x-guest-layout>
