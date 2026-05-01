<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Panel Tindak Lanjut
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @if (session('status'))
                <div class="bg-green-50 text-green-700 px-4 py-3 rounded-md border border-green-200">
                    {{ session('status') }}
                </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <form method="GET" class="flex flex-wrap gap-3 items-end">
                    <div>
                        <x-input-label for="status" :value="'Filter Status'" />
                        <select id="status" name="status" class="mt-1 border-gray-300 focus:border-orange-500 focus:ring-orange-500 rounded-md shadow-sm">
                            <option value="">Semua</option>
                            <option value="assigned" @selected($status === 'assigned')>Baru (Ditugaskan)</option>
                            <option value="in_progress" @selected($status === 'in_progress')>Sedang Diproses</option>
                            <option value="resolved" @selected($status === 'resolved')>Selesai</option>
                        </select>
                    </div>
                    <x-primary-button>Terapkan</x-primary-button>
                </form>
            </div>

            <div class="space-y-4">
                @forelse ($complaints as $complaint)
                    @php
                        $lastProgress = $complaint->actions()->whereIn('action_type', ['in_progress', 'progress_updated'])->first()?->notes;
                        $lastResolve = $complaint->actions()->whereIn('action_type', ['resolved', 'resolve_updated'])->first()?->notes;
                    @endphp
                    <div class="bg-white shadow-sm sm:rounded-lg p-6">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">{{ $complaint->title }}</h3>
                                <p class="text-xs text-gray-500">Oleh: {{ $complaint->reporter->name }} | {{ $complaint->created_at->format('d M Y') }}</p>
                            </div>
                            <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider border {{ $complaint->status === 'resolved' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-blue-50 text-blue-700 border-blue-200' }}">
                                {{ $complaint->status_label }}
                            </span>
                        </div>
                        
                        <div class="bg-gray-50 p-4 rounded-xl border border-gray-100 mb-6">
                            <p class="text-sm text-gray-700 leading-relaxed">{{ $complaint->description }}</p>
                            <a href="{{ route('complaints.show', $complaint) }}" class="mt-3 inline-block text-xs font-bold text-orange-600 hover:underline">Lihat Detail Laporan &rarr;</a>
                        </div>

                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <form method="POST" action="{{ route('instansi.complaints.progress', $complaint) }}" class="space-y-3">
                                @csrf
                                <x-input-label :value="'Update Progres Lapangan'" />
                                <textarea name="notes" rows="3" class="block w-full border-gray-300 focus:border-orange-500 focus:ring-orange-500 rounded-xl shadow-sm text-sm" placeholder="Catatan progres..." {{ $complaint->status === 'resolved' ? 'disabled opacity-50' : '' }} required>{{ $lastProgress }}</textarea>
                                <x-primary-button :disabled="$complaint->status === 'resolved'">
                                    {{ $complaint->status === 'in_progress' ? 'Simpan Perubahan' : 'Update Proses' }}
                                </x-primary-button>
                            </form>

                            <form method="POST" action="{{ route('instansi.complaints.resolve', $complaint) }}" class="space-y-3">
                                @csrf
                                <x-input-label :value="'Hasil Akhir / Penyelesaian'" />
                                <textarea name="notes" rows="3" class="block w-full border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 rounded-xl shadow-sm text-sm" placeholder="Ringkasan hasil akhir..." required>{{ $lastResolve }}</textarea>
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-emerald-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase hover:bg-emerald-700">
                                    {{ $complaint->status === 'resolved' ? 'Simpan Perubahan' : 'Tandai Selesai' }}
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="bg-white shadow-sm sm:rounded-lg p-6 text-sm text-gray-500">
                        Tidak ada pengaduan yang sedang ditangani instansi Anda.
                    </div>
                @endforelse
            </div>

            <div>
                {{ $complaints->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
