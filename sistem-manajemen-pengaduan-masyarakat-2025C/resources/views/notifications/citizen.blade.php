<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Inbox
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <div class="space-y-3">
                    @forelse ($actions as $action)
                        @php
                            $message = data_get($action->meta, 'message_for_citizen') ?: $action->notes ?: 'Ada pembaruan status laporan.';
                        @endphp
                        <div class="border border-gray-200 rounded-md p-4">
                            <p class="text-sm font-semibold text-gray-900">
                                {{ $action->complaint?->title ?? 'Laporan' }}
                            </p>
                            <p class="text-sm text-gray-700 mt-1">{{ $message }}</p>
                            <div class="mt-2 flex flex-wrap gap-3 text-xs text-gray-500">
                                <span>{{ $action->action_label }}</span>
                                <span>{{ $action->created_at->format('d M Y H:i') }}</span>
                                @if ($action->complaint)
                                    <a class="text-orange-600 hover:text-orange-700 underline" href="{{ route('complaints.show', $action->complaint) }}">
                                        Lihat Laporan
                                    </a>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500">Belum ada notifikasi.</p>
                    @endforelse
                </div>

                <div class="mt-4">
                    {{ $actions->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
