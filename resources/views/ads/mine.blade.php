<x-akwaba-layout title="Mes annonces">
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-extrabold">Mes annonces</h1>
        <a href="{{ route('ads.create') }}" class="rounded-full bg-ci-green px-4 py-2 text-sm font-semibold text-white hover:opacity-90">+ Publier</a>
    </div>

    @forelse ($ads as $ad)
        <div class="mb-5">
            @include('ads._card')
            <div class="mt-2 flex justify-end gap-2 text-sm font-semibold">
                <a href="{{ route('ads.edit', $ad) }}" class="rounded-full border border-ci-orange px-4 py-1 text-ci-orange hover:bg-orange-50">Modifier</a>
                <form method="POST" action="{{ route('ads.destroy', $ad) }}" onsubmit="return confirm('Supprimer définitivement cette annonce ?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="rounded-full border border-red-300 px-4 py-1 text-red-600 hover:bg-red-50">Supprimer</button>
                </form>
            </div>
        </div>
    @empty
        <p class="rounded-2xl bg-white p-8 text-center text-gray-500">Tu n'as pas encore publié d'annonce.</p>
    @endforelse

    <div class="mt-8">{{ $ads->links() }}</div>
</x-akwaba-layout>
