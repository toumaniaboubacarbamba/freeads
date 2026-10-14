<x-guest-layout>
    {{-- Onglets --}}
    <div class="mb-6 grid grid-cols-2 border-b border-outline-variant text-center font-bold">
        <a href="{{ route('login') }}" class="pb-3 text-on-surface-variant hover:text-primary-container">Se connecter</a>
        <span class="border-b-2 border-primary-container pb-3 text-primary-container">Créer un compte</span>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-5">
        @csrf

        <x-auth-field name="login" label="Identifiant" icon="person"
                      autocomplete="username" placeholder="ex : awa_coulibaly"
                      hint="Lettres, chiffres, tirets et underscores. C'est le nom affiché sur tes annonces."
                      :autofocus="true" />

        <x-auth-field name="phone" type="tel" label="Numéro de téléphone" icon="call" prefix="+225"
                      autocomplete="tel" placeholder="0707070707"
                      hint="10 chiffres, sans espaces (Orange, MTN, Moov). Il servira à te contacter." />

        <x-auth-field name="email" type="email" label="Adresse email" icon="mail"
                      autocomplete="email" placeholder="ex : awa@gmail.com"
                      hint="Nous t'envoyons un lien de confirmation à cette adresse." />

        <x-password-field name="password" label="Mot de passe" autocomplete="new-password"
                          placeholder="8 caractères minimum" />

        <x-password-field name="password_confirmation" label="Confirmer le mot de passe" autocomplete="new-password" />

        <button type="submit"
                class="flex w-full items-center justify-center gap-2 rounded-xl bg-primary-container px-4 py-3 font-bold text-white transition hover:bg-[#d86b00]">
            <span class="material-symbols-outlined text-[20px]">how_to_reg</span> Créer mon compte Akwaba
        </button>
    </form>

    <p class="mt-6 text-center text-sm text-on-surface-variant">
        Déjà un compte ?
        <a href="{{ route('login') }}" class="font-bold text-primary hover:underline">Se connecter</a>
    </p>
</x-guest-layout>
