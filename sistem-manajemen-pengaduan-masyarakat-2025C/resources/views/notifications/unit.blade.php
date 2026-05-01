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
                            $message = data_get($action->meta, 'message_for_unit') ?: $action->notes ?: 'Ada pembaruan untuk laporan yang ditugaskan.';
                        @endphp
                        <div class="border border-gray-200 rounded-md p-4">
                            <p class="text-sm font-semibold text-gray-900">
                                {{ $action->complaint?->title ?? 'Laporan' }}
                            </p>
                            <p class="text-sm text-gray-700 mt-1">{{ $message }}</p>
                            <div class="mt-2 flex flex-wrap gap-3 text-xs text-gray-500">
                                <span>{{ ucfirst(str_replace('_', ' ', $action->action_type)) }}</span>
                                <span>{{ $action->created_at->format('d M Y H:i') }}</span>
                                <a class="text-orange-600 hover:text-orange-700 underline" href="{{ route('instansi.complaints.index') }}">
                                    Buka Panel Tindak Lanjut
                                </a>
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
