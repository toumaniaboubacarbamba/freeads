@props([
    'name',
    'label',
    'icon' => 'person',
    'type' => 'text',
    'autocomplete' => null,
    'placeholder' => '',
    'hint' => null,
    'prefix' => null,
    'autofocus' => false,
])

<div>
    <label for="{{ $name }}" class="text-sm font-bold">{{ $label }}</label>

    <div class="mt-1 flex">
        @if ($prefix)
            <span class="flex items-center rounded-l-xl border-[1.5px] border-r-0 border-outline-variant bg-surface-container-low px-3 text-sm font-bold">{{ $prefix }}</span>
        @endif

        <div class="relative flex-1">
            <span class="material-symbols-outlined pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-outline">{{ $icon }}</span>
            <input id="{{ $name }}" name="{{ $name }}" type="{{ $type }}" value="{{ old($name) }}" required
                   @if ($autocomplete) autocomplete="{{ $autocomplete }}" @endif
                   @if ($autofocus) autofocus @endif
                   placeholder="{{ $placeholder }}"
                   class="block w-full {{ $prefix ? 'rounded-r-xl' : 'rounded-xl' }} border-[1.5px] border-outline-variant bg-white py-3 pl-11 pr-4 text-sm focus:border-primary-container focus:ring-4 focus:ring-primary-container/15">
        </div>
    </div>

    @if ($hint)
        <p class="mt-1 text-xs text-on-surface-variant">{{ $hint }}</p>
    @endif
    @error($name)
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>
