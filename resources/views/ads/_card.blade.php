<a href="{{ route('ads.show', $ad) }}"
   class="group flex flex-col gap-4 rounded-2xl border border-orange-100 bg-white p-3 shadow-sm transition hover:border-ci-orange hover:shadow-md sm:flex-row">
    <div class="relative h-48 shrink-0 overflow-hidden rounded-xl bg-orange-50 sm:h-40 sm:w-64">
        @if ($ad->cover_url)
            <img src="{{ $ad->cover_url }}" alt="{{ $ad->title }}" class="h-full w-full object-cover transition group-hover:scale-105">
        @else
            <div class="flex h-full items-center justify-center text-4xl">📷</div>
        @endif
        <span class="absolute bottom-2 left-1/2 -translate-x-1/2 whitespace-nowrap rounded-full bg-white px-4 py-1 text-sm font-bold shadow">
            {{ $ad->price_formatted }}
        </span>
    </div>

    <div class="flex min-w-0 flex-col justify-between py-1">
        <div>
            <div class="flex flex-wrap items-center gap-2">
                <h3 class="truncate text-lg font-bold">{{ $ad->title }}</h3>
                <span class="text-xs font-semibold uppercase tracking-wide text-ci-orange">{{ $ad->category->name }}</span>
            </div>
            <p class="mt-1 line-clamp-2 text-sm text-gray-600">{{ $ad->description }}</p>
        </div>
        <p class="mt-3 text-sm text-gray-500">
            📍 {{ $ad->location }} · {{ $ad->condition_label }} · Par
            <span class="font-semibold text-ci-green">{{ $ad->user->login }}</span>
        </p>
    </div>
</a>
