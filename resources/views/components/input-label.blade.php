@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-bold text-sm text-orange-600']) }}>
    {{ $value ?? $slot }}
</label>
