@php
    $user = auth()->user();
    $initial = mb_strtoupper(mb_substr($user->login, 0, 1));
    $card = 'rounded-2xl border border-outline-variant/50 bg-surface-container-lowest p-6 shadow-[0_2px_8px_rgba(43,30,22,0.04)]';
    $field = 'block w-full rounded-xl border-[1.5px] border-outline-variant bg-white py-3 pl-11 pr-4 text-sm focus:border-primary-container focus:ring-4 focus:ring-primary-container/15';
    $icon = 'material-symbols-outlined pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-outline';
    $label = 'text-sm font-bold';
    $err = 'mt-1 text-sm text-red-600';
@endphp

<x-akwaba-layout title="Mon profil" robots="noindex, nofollow">
    <div class="mx-auto max-w-3xl space-y-6">

        {{-- En-tête --}}
        <div class="flex items-center gap-4">
            <span class="grid h-16 w-16 shrink-0 place-items-center rounded-full bg-primary-container text-2xl font-extrabold text-white">{{ $initial }}</span>
            <div class="min-w-0">
                <h1 class="truncate text-3xl font-extrabold tracking-tight">Mon profil</h1>
                <p class="text-sm text-on-surface-variant">Gère tes informations de contact et la sécurité de ton compte.</p>
            </div>
        </div>

        {{-- Informations personnelles --}}
        <section class="{{ $card }}">
            <h2 class="mb-1 flex items-center gap-2 text-lg font-bold">
                <span class="material-symbols-outlined text-primary-container">badge</span> Informations personnelles
            </h2>
            <p class="mb-5 text-sm text-on-surface-variant">Ton identifiant est affiché sur tes annonces, ton téléphone sert aux acheteurs pour te joindre.</p>

            <form method="POST" action="{{ route('profile.update') }}" class="space-y-5">
                @csrf
                @method('PATCH')

                <div>
                    <label for="login" class="{{ $label }}">Identifiant</label>
                    <div class="relative mt-1">
                        <span class="{{ $icon }}">person</span>
                        <input id="login" name="login" type="text" required autocomplete="username"
                               value="{{ old('login', $user->login) }}" class="{{ $field }}">
                    </div>
                    @error('login') <p class="{{ $err }}">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="phone" class="{{ $label }}">Numéro de téléphone</label>
                    <div class="mt-1 flex">
                        <span class="flex items-center rounded-l-xl border-[1.5px] border-r-0 border-outline-variant bg-surface-container-low px-3 text-sm font-bold">+225</span>
                        <div class="relative flex-1">
                            <span class="{{ $icon }}">call</span>
                            <input id="phone" name="phone" type="tel" required autocomplete="tel"
                                   value="{{ old('phone', $user->phone) }}" class="{{ str_replace('rounded-xl', 'rounded-r-xl', $field) }}">
                        </div>
                    </div>
                    <p class="mt-1 text-xs text-on-surface-variant">10 chiffres, sans espaces.</p>
                    @error('phone') <p class="{{ $err }}">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="email" class="{{ $label }}">Adresse email</label>
                    <div class="relative mt-1">
                        <span class="{{ $icon }}">mail</span>
                        <input id="email" name="email" type="email" required autocomplete="email"
                               value="{{ old('email', $user->email) }}" class="{{ $field }}">
                    </div>
                    @error('email') <p class="{{ $err }}">{{ $message }}</p> @enderror

                    @unless ($user->hasVerifiedEmail())
                        <div class="mt-3 flex items-start gap-2 rounded-xl border border-amber-200 bg-amber-50 p-3 text-sm text-amber-900">
                            <span class="material-symbols-outlined text-[20px]">warning</span>
                            <p>
                                Ton adresse email n'est pas encore vérifiée.
                                <button form="send-verification" type="submit" class="font-bold underline">Renvoyer le lien de confirmation</button>
                            </p>
                        </div>
                    @endunless
                </div>

                <div class="flex justify-end">
                    <button type="submit"
                            class="flex items-center gap-2 rounded-xl bg-secondary px-6 py-3 font-bold text-white transition hover:opacity-90">
                        <span class="material-symbols-outlined text-[20px]">save</span> Enregistrer
                    </button>
                </div>
            </form>

            {{-- Formulaire séparé (un formulaire ne peut pas en contenir un autre) --}}
            <form id="send-verification" method="POST" action="{{ route('verification.send') }}">@csrf</form>
        </section>

        {{-- Mot de passe --}}
        <section class="{{ $card }}">
            <h2 class="mb-1 flex items-center gap-2 text-lg font-bold">
                <span class="material-symbols-outlined text-primary-container">lock</span> Mot de passe
            </h2>
            <p class="mb-5 text-sm text-on-surface-variant">Choisis un mot de passe long et unique pour protéger ton compte.</p>

            <form method="POST" action="{{ route('password.update') }}" class="space-y-5">
                @csrf
                @method('PUT')

                <x-password-field name="current_password" id="update_current_password" bag="updatePassword"
                                  label="Mot de passe actuel" autocomplete="current-password" />
                <x-password-field name="password" id="update_password" bag="updatePassword"
                                  label="Nouveau mot de passe" autocomplete="new-password" placeholder="8 caractères minimum" />
                <x-password-field name="password_confirmation" id="update_password_confirmation" bag="updatePassword"
                                  label="Confirmer le nouveau mot de passe" autocomplete="new-password" />

                <div class="flex justify-end">
                    <button type="submit"
                            class="flex items-center gap-2 rounded-xl bg-primary-container px-6 py-3 font-bold text-white transition hover:bg-[#d86b00]">
                        <span class="material-symbols-outlined text-[20px]">key</span> Changer le mot de passe
                    </button>
                </div>
            </form>
        </section>

        {{-- Suppression du compte --}}
        <section class="rounded-2xl border border-red-200 bg-red-50/60 p-6"
                 x-data="{ open: {{ $errors->userDeletion->isNotEmpty() ? 'true' : 'false' }} }">
            <h2 class="mb-1 flex items-center gap-2 text-lg font-bold text-red-700">
                <span class="material-symbols-outlined">warning</span> Supprimer mon compte
            </h2>
            <p class="text-sm text-on-surface-variant">
                Cette action est définitive : ton compte, toutes tes annonces et leurs photos seront supprimés.
            </p>

            <button type="button" x-show="!open" @click="open = true"
                    class="mt-4 flex items-center gap-2 rounded-xl border border-red-300 bg-white px-5 py-2.5 font-bold text-red-600 hover:bg-red-50">
                <span class="material-symbols-outlined text-[20px]">delete_forever</span> Supprimer mon compte
            </button>

            <form x-show="open" x-cloak method="POST" action="{{ route('profile.destroy') }}" class="mt-5 space-y-4">
                @csrf
                @method('DELETE')

                <x-password-field name="password" id="delete_password" bag="userDeletion"
                                  label="Confirme avec ton mot de passe" autocomplete="current-password" />

                <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                    <button type="button" @click="open = false"
                            class="rounded-xl border border-outline-variant bg-white px-6 py-3 font-semibold hover:bg-surface-container-low">Annuler</button>
                    <button type="submit"
                            class="flex items-center justify-center gap-2 rounded-xl bg-red-600 px-6 py-3 font-bold text-white hover:bg-red-700">
                        <span class="material-symbols-outlined text-[20px]">delete_forever</span> Oui, supprimer définitivement
                    </button>
                </div>
            </form>
        </section>
    </div>
</x-akwaba-layout>
