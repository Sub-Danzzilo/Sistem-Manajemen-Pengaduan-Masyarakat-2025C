@props([
    'name',
    'title',
    'description',
    'confirmText' => 'Ya',
    'cancelText' => 'Batal',
    'confirmType' => 'danger' // 'danger' atau 'primary'
])

<x-modal :name="$name" focusable>
    <div class="p-6">
        <h2 class="text-lg font-medium text-gray-900">
            {{ $title }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ $description }}
        </p>

        <div class="mt-6 flex justify-end">
            <x-secondary-button x-on:click="$dispatch('close')">
                {{ $cancelText }}
            </x-secondary-button>

            @if($confirmType === 'danger')
                <x-danger-button class="ms-3">
                    {{ $confirmText }}
                </x-danger-button>
            @else
                <button type="submit" class="ms-3 inline-flex items-center px-4 py-2 bg-orange-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-orange-700 focus:bg-orange-700 active:bg-orange-900 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    {{ $confirmText }}
                </button>
            @endif
        </div>
    </div>
</x-modal>
