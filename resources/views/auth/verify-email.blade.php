<x-guest-layout>
    <div class="text-center">
        <span class="mx-auto grid h-16 w-16 place-items-center rounded-full bg-surface-container text-primary-container">
            <span class="material-symbols-outlined text-4xl">mark_email_unread</span>
        </span>
        <h1 class="mt-4 text-2xl font-extrabold">Confirme ton adresse email</h1>
        <p class="mt-2 text-sm text-on-surface-variant">
            Merci pour ton inscription ! Clique sur le lien que nous t'avons envoyé par email pour activer ton compte.
            Tu ne l'as pas reçu ? Nous pouvons t'en renvoyer un.
        </p>
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mt-5 rounded-xl border border-secondary-fixed-dim bg-secondary-container/40 px-4 py-3 text-sm text-on-secondary-container">
            Un nouveau lien de confirmation vient d'être envoyé à ton adresse email.
        </div>
    @endif

    <div class="mt-6 space-y-3">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit"
                    class="flex w-full items-center justify-center gap-2 rounded-xl bg-primary-container px-4 py-3 font-bold text-white transition hover:bg-[#d86b00]">
                <span class="material-symbols-outlined text-[20px]">send</span> Renvoyer l'email de confirmation
            </button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                    class="flex w-full items-center justify-center gap-2 rounded-xl border border-outline-variant px-4 py-3 font-semibold hover:bg-surface-container-low">
                <span class="material-symbols-outlined text-[20px]">logout</span> Se déconnecter
            </button>
        </form>
    </div>
</x-guest-layout>
