@php
    $badge = [
        'new'  => 'bg-secondary',
        'good' => 'bg-primary-container',
        'used' => 'bg-inverse-surface/80',
    ][$ad->condition] ?? 'bg-inverse-surface/80';
@endphp

<a href="{{ route('ads.show', $ad) }}"
   class="group flex flex-col overflow-hidden rounded-2xl border border-outline-variant/50 bg-surface-container-lowest shadow-[0_2px_8px_rgba(43,30,22,0.04)] transition hover:-translate-y-0.5 hover:shadow-[0_8px_20px_rgba(43,30,22,0.08)]">

    <div class="relative aspect-[4/3] overflow-hidden bg-surface-container">
        @if ($ad->cover_url)
            <img src="{{ $ad->cover_url }}" alt="{{ $ad->title }}" loading="lazy"
                 class="h-full w-full object-cover transition duration-300 group-hover:scale-105">
        @else
            <div class="flex h-full items-center justify-center">
                <span class="material-symbols-outlined text-5xl text-outline">image</span>
            </div>
        @endif
        <span class="absolute bottom-3 left-3 rounded-md px-2 py-1 text-xs font-bold text-white {{ $badge }}">
            {{ $ad->condition_label }}
        </span>
    </div>

    <div class="flex flex-1 flex-col p-4">
        <div class="flex items-center justify-between gap-2 text-xs">
            <span class="font-bold uppercase tracking-wide text-primary">{{ $ad->category->name }}</span>
            <span class="flex items-center gap-1 text-on-surface-variant">
                <span class="material-symbols-outlined text-[14px]">schedule</span>{{ $ad->created_at->diffForHumans() }}
            </span>
        </div>

        <h3 class="mt-2 line-clamp-2 text-base font-bold leading-snug">{{ $ad->title }}</h3>

        <p class="mt-2 flex items-center gap-1 text-sm text-on-surface-variant">
            <span class="material-symbols-outlined text-[18px] text-secondary">location_on</span>{{ $ad->location }}
        </p>

        <div class="mt-auto flex items-end justify-between gap-2 border-t border-outline-variant/40 pt-3">
            <span class="text-lg font-extrabold leading-6 text-primary-container">{{ $ad->price_formatted }}</span>
            <span class="flex max-w-[45%] items-center gap-1 truncate text-xs font-semibold text-secondary">
                <span class="material-symbols-outlined text-[16px]">person</span>{{ $ad->user->login }}
            </span>
        </div>
    </div>
</a>
