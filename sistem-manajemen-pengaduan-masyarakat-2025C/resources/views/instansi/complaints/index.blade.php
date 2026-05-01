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
                        $statusClass = match ($complaint->status) {
                            'assigned', 'in_progress' => 'bg-blue-100 text-blue-800',
                            'resolved' => 'bg-emerald-100 text-emerald-800',
                            default => 'bg-gray-100 text-gray-800',
                        };
                    @endphp
                    <div class="bg-white shadow-sm sm:rounded-lg p-6">
                        <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
                            <!-- Sisi Kiri: Detail Laporan -->
                            <div class="w-full lg:w-3/5">
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-xl font-bold text-gray-900">{{ $complaint->title }}</h3>
                                    <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-bold uppercase tracking-wider {{ $statusClass }}">
                                        {{ $complaint->status_label }}
                                    </span>
                                </div>

                                <div class="grid grid-cols-2 gap-y-2 gap-x-4 mb-4 text-xs">
                                    <div class="flex items-center gap-2 text-gray-600">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                        <span class="font-medium text-gray-900">{{ $complaint->reporter->name }}</span>
                                    </div>
                                    <div class="flex items-center gap-2 text-gray-600">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                                        <span>{{ $complaint->category ?: 'Belum ada kategori' }}</span>
                                    </div>
                                    <div class="flex items-center gap-2 text-gray-600">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                        <span class="truncate">{{ $complaint->location_text ?: 'Lokasi tidak spesifik' }}</span>
                                    </div>
                                    <div class="flex items-center gap-2 text-gray-600">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        <span>{{ $complaint->created_at->format('d M Y, H:i') }}</span>
                                    </div>
                                </div>

                                <div class="bg-gray-50 p-4 rounded-xl border border-gray-100 text-sm text-gray-700 leading-relaxed mb-6 relative">
                                    {{ $complaint->description }}
                                    <a href="{{ route('complaints.show', $complaint) }}" class="absolute bottom-2 right-4 text-[10px] font-bold text-orange-600 hover:underline">Riwayat Lengkap &rarr;</a>
                                </div>

                                <!-- Attachments Preview -->
                                @if($complaint->attachments->count() > 0)
                                    <div class="space-y-2">
                                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Lampiran ({{ $complaint->attachments->count() }})</p>
                                        <div class="flex flex-wrap gap-2">
                                            @foreach($complaint->attachments as $file)
                                                @php $isImage = $file->attachment_type === 'image'; @endphp
                                                <a href="{{ asset('storage/'.$file->file_path) }}" target="_blank" class="group relative w-16 h-16 rounded-lg overflow-hidden border border-gray-200 hover:border-orange-400 transition-colors shadow-sm">
                                                    @if($isImage)
                                                        <img src="{{ asset('storage/'.$file->file_path) }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
                                                    @else
                                                        <div class="w-full h-full bg-gray-50 flex items-center justify-center">
                                                            @if($file->attachment_type === 'video')
                                                                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                                            @elseif($file->attachment_type === 'audio')
                                                                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"></path></svg>
                                                            @else
                                                                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                                                            @endif
                                                        </div>
                                                    @endif
                                                    <div class="absolute inset-0 bg-black/0 group-hover:bg-black/10 transition-colors"></div>
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <!-- Sisi Kanan: Aksi (Smart Form) -->
                            <div class="w-full lg:w-2/5" x-data="{ 
                                isResolving: {{ $complaint->status === 'resolved' ? 'true' : 'false' }},
                                isLocked: {{ $complaint->status === 'resolved' ? 'true' : 'false' }}
                            }">
                                <!-- Checkbox Toggle -->
                                <div class="mb-4 pb-4 border-b border-gray-100" x-show="!isLocked">
                                    <label class="inline-flex items-center cursor-pointer group">
                                        <input type="checkbox" x-model="isResolving" class="rounded border-gray-300 text-emerald-600 shadow-sm focus:ring-emerald-500 transition-colors">
                                        <span class="ml-2 text-sm font-semibold text-gray-600 group-hover:text-gray-900 transition-colors">Tandai sebagai selesai?</span>
                                    </label>
                                </div>

                                <!-- Form Update Progress -->
                                <form x-show="!isResolving" x-cloak method="POST" action="{{ route('instansi.complaints.progress', $complaint) }}" class="space-y-3">
                                    @csrf
                                    <x-input-label :value="'Update Progres Lapangan'" />
                                    <textarea name="notes" rows="4" class="block w-full border-gray-300 focus:border-orange-500 focus:ring-orange-500 rounded-xl shadow-sm text-sm" placeholder="Catat tindakan atau progres yang telah dilakukan..." :disabled="isLocked" required>{{ $lastProgress }}</textarea>
                                    <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 bg-orange-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase hover:bg-orange-700 transition-colors disabled:opacity-50" :disabled="isLocked">
                                        {{ $complaint->status === 'in_progress' ? 'Simpan Perubahan' : 'Update Proses' }}
                                    </button>
                                </form>

                                <!-- Form Hasil Akhir -->
                                <form x-show="isResolving" x-cloak method="POST" action="{{ route('instansi.complaints.resolve', $complaint) }}" class="space-y-3">
                                    @csrf
                                    <x-input-label :value="'Hasil Akhir / Penyelesaian'" />
                                    <textarea name="notes" rows="4" class="block w-full border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 rounded-xl shadow-sm text-sm" placeholder="Jelaskan ringkasan hasil akhir bahwa masalah telah teratasi..." :disabled="isLocked" required>{{ $lastResolve }}</textarea>
                                    
                                    <button x-show="!isLocked" type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 bg-emerald-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase hover:bg-emerald-700 transition-colors">
                                        Selesaikan Laporan
                                    </button>
                                    
                                    <div x-show="isLocked" class="text-center mt-2 p-2 bg-emerald-50 rounded-lg border border-emerald-100">
                                        <span class="text-xs font-bold text-emerald-700 flex items-center justify-center gap-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                            Laporan Telah Selesai
                                        </span>
                                    </div>
                                </form>
                            </div>
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
