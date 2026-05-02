@props(['value' => null])

<div class="relative">
    <x-text-input {{ $attributes->merge(['class' => 'block mt-1 w-full pr-10']) }}
                    type="password"
                    :value="$value"
    />
    <button
        type="button"
        class="absolute inset-y-0 right-0 flex items-center pr-3 cursor-pointer text-gray-500 hover:text-gray-700"
        onclick="togglePassword(this)"
        aria-label="Tampilkan password"
    >
        <!-- Eye (show) -->
        <svg data-eye-open xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-5 h-5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1 1 0 010-.644C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178a1 1 0 010 .644C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.964-7.178z" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
        </svg>
    
        <!-- Eye off (hide) -->
        <svg data-eye-closed xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-5 h-5 hidden">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 3l18 18" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.584 10.587a2 2 0 102.829 2.829" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M9.88 5.09A9.953 9.953 0 0112 4.5c4.638 0 8.573 3.007 9.963 7.178.177.53.177 1.114 0 1.644a10.05 10.05 0 01-4.043 5.06M6.228 6.228A10.054 10.054 0 002.037 11.68a1 1 0 000 .644C3.423 16.49 7.36 19.5 12 19.5c1.57 0 3.058-.345 4.394-.964" />
        </svg>
    </button>
</div>