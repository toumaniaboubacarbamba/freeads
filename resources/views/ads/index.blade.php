<x-akwaba-layout title="Accueil">
    <section class="mb-8 rounded-3xl bg-gradient-to-r from-ci-orange to-orange-400 p-8 text-white">
        <h1 class="text-3xl font-extrabold">Achetez et vendez près de chez vous</h1>
        <p class="mt-2 text-orange-50">Publiez gratuitement à Abidjan et partout en Côte d'Ivoire.</p>
    </section>

    <div class="space-y-4">
        @forelse ($ads as $ad)
            @include('ads._card')
        @empty
            <p class="rounded-2xl bg-white p-8 text-center text-gray-500">
                Aucune annonce pour le moment.
                <a href="{{ route('ads.create') }}" class="font-semibold text-ci-green">Sois le premier à publier !</a>
            </p>
        @endforelse
    </div>

    <div class="mt-8">{{ $ads->links() }}</div>
</x-akwaba-layout>
