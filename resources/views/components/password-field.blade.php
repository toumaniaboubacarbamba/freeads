@props([
    'name',
    'label',
    'id' => null,
    'bag' => 'default',
    'autocomplete' => 'current-password',
    'placeholder' => '',
    'hint' => null,
])
@php $inputId = $id ?? $name; @endphp

<div x-data="{ show: false }">
    <label for="{{ $inputId }}" class="text-sm font-bold">{{ $label }}</label>

    <div class="relative mt-1">
        <span class="material-symbols-outlined pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-outline">lock</span>
        <input id="{{ $inputId }}" name="{{ $name }}" type="password" :type="show ? 'text' : 'password'" required
               autocomplete="{{ $autocomplete }}" placeholder="{{ $placeholder }}"
               class="block w-full rounded-xl border-[1.5px] border-outline-variant bg-white py-3 pl-11 pr-12 text-sm focus:border-primary-container focus:ring-4 focus:ring-primary-container/15">
        <button type="button" @click="show = !show" :aria-label="show ? 'Masquer le mot de passe' : 'Afficher le mot de passe'"
                class="absolute right-3 top-1/2 -translate-y-1/2 text-outline hover:text-on-surface">
            <span class="material-symbols-outlined" x-text="show ? 'visibility_off' : 'visibility'">visibility</span>
        </button>
    </div>

    @if ($hint)
        <p class="mt-1 text-xs text-on-surface-variant">{{ $hint }}</p>
    @endif
    @error($name, $bag)
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>
