<x-akwaba-layout :title="$ad->title">
    <div class="grid gap-8 lg:grid-cols-3">
        <div class="lg:col-span-2" x-data="{ current: '{{ $ad->cover_url }}' }">
            <div class="overflow-hidden rounded-2xl border border-orange-100 bg-white">
                @if ($ad->photos->isNotEmpty())
                    <img :src="current" alt="{{ $ad->title }}" class="h-96 w-full object-cover">
                @endif
            </div>

            @if ($ad->photos->count() > 1)
                <div class="mt-3 flex gap-2 overflow-x-auto">
                    @foreach ($ad->photos as $photo)
                        <button type="button" @click="current = '{{ $photo->url }}'" class="shrink-0 overflow-hidden rounded-xl border-2 border-transparent hover:border-ci-orange">
                            <img src="{{ $photo->url }}" alt="" class="h-20 w-20 object-cover">
                        </button>
                    @endforeach
                </div>
            @endif

            <div class="mt-6 rounded-2xl border border-orange-100 bg-white p-6">
                <h2 class="mb-2 text-lg font-bold">Description</h2>
                <p class="text-gray-700">{!! nl2br(e($ad->description)) !!}</p>
            </div>
        </div>

        <aside class="space-y-4">
            <div class="rounded-2xl border border-orange-100 bg-white p-6">
                <span class="text-xs font-semibold uppercase tracking-wide text-ci-orange">{{ $ad->category->name }}</span>
                <h1 class="mt-1 text-2xl font-extrabold">{{ $ad->title }}</h1>
                <p class="mt-2 text-3xl font-extrabold text-ci-green">{{ $ad->price_formatted }}</p>
                <ul class="mt-4 space-y-1 text-sm text-gray-600">
                    <li>📍 {{ $ad->location }}</li>
                    <li>🏷️ {{ $ad->condition_label }}</li>
                    <li>🕒 Publiée {{ $ad->created_at->diffForHumans() }}</li>
                </ul>
            </div>

            <div class="rounded-2xl border border-orange-100 bg-white p-6">
                <p class="text-sm text-gray-500">Vendeur</p>
                <p class="text-lg font-bold">{{ $ad->user->login }}</p>

                <div class="mt-4 grid gap-2">
                    <a href="tel:{{ $ad->user->phone }}" class="rounded-full bg-ci-orange px-4 py-2 text-center font-semibold text-white hover:opacity-90">📞 Appeler</a>
                    <a href="https://wa.me/{{ $ad->user->whatsapp_number }}?text={{ urlencode('Bonjour, je suis intéressé par votre annonce « '.$ad->title.' ».') }}"
                       target="_blank" rel="noopener"
                       class="rounded-full bg-ci-green px-4 py-2 text-center font-semibold text-white hover:opacity-90">💬 WhatsApp</a>
                </div>
            </div>

            @can('update', $ad)
                <div class="flex gap-2">
                    <a href="{{ route('ads.edit', $ad) }}" class="flex-1 rounded-full border border-ci-orange px-4 py-2 text-center font-semibold text-ci-orange hover:bg-orange-50">Modifier</a>
                    <form method="POST" action="{{ route('ads.destroy', $ad) }}" class="flex-1" onsubmit="return confirm('Supprimer définitivement cette annonce ?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full rounded-full border border-red-300 px-4 py-2 font-semibold text-red-600 hover:bg-red-50">Supprimer</button>
                    </form>
                </div>
            @endcan
        </aside>
    </div>
</x-akwaba-layout>
