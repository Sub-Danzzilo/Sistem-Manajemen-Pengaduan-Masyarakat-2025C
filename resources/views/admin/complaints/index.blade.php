<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Panel Verifikasi Admin
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <form method="GET" class="flex flex-wrap gap-3 items-end">
                    <div>
                        <x-input-label for="status" :value="'Filter Status'" />
                        <select id="status" name="status" class="mt-1 border-gray-300 focus:border-orange-500 focus:ring-orange-500 rounded-md shadow-sm">
                            <option value="">Semua</option>
                            <option value="submitted" @selected($status === 'submitted')>Menunggu Verifikasi</option>
                            <option value="verified" @selected($status === 'verified')>Diverifikasi</option>
                            <option value="assigned" @selected($status === 'assigned')>Ditugaskan</option>
                            <option value="in_progress" @selected($status === 'in_progress')>Diproses</option>
                            <option value="resolved" @selected($status === 'resolved')>Selesai</option>
                            <option value="rejected" @selected($status === 'rejected')>Ditolak</option>
                        </select>
                    </div>
                    <x-primary-button>Terapkan</x-primary-button>
                </form>
            </div>

            <div class="space-y-4">
                @forelse ($complaints as $complaint)
                    @php
                        $statusClass = match ($complaint->status) {
                            'submitted' => 'bg-yellow-100 text-yellow-800',
                            'verified', 'assigned', 'in_progress' => 'bg-blue-100 text-blue-800',
                            'resolved' => 'bg-emerald-100 text-emerald-800',
                            'rejected' => 'bg-red-100 text-red-800',
                            default => 'bg-gray-100 text-gray-800',
                        };
                    @endphp
                    <div id="complaint-{{ $complaint->id }}" class="bg-white shadow-sm sm:rounded-lg p-6 scroll-mt-20">
                        <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
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
                                    <a href="{{ route('complaints.show', $complaint) }}" class="absolute bottom-2 right-4 text-[10px] font-bold text-orange-600 hover:underline">Detail Lengkap &rarr;</a>
                                </div>

                                <!-- Attachments Preview -->
                                @if($complaint->attachments->count() > 0)
                                    <div class="space-y-2" x-data="{ 
                                        downloading: false,
                                        files: [
                                            @foreach($complaint->attachments as $att)
                                                { url: '{{ route('attachments.download', ['account' => request()->route('account'), 'role' => request()->route('role'), 'attachment' => $att->id]) }}', name: '{{ $att->original_name }}' },
                                            @endforeach
                                        ],
                                        async downloadAll() {
                                            this.downloading = true;
                                            for (let i = 0; i < this.files.length; i++) {
                                                const file = this.files[i];
                                                const link = document.createElement('a');
                                                link.href = file.url;
                                                link.setAttribute('download', file.name);
                                                document.body.appendChild(link);
                                                link.click();
                                                document.body.removeChild(link);
                                                await new Promise(resolve => setTimeout(resolve, 800));
                                            }
                                            this.downloading = false;
                                        }
                                    }">
                                        <div class="flex items-center justify-between">
                                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Lampiran ({{ $complaint->attachments->count() }})</p>
                                            @if($complaint->attachments->count() > 1)
                                                <button @click="downloadAll" :disabled="downloading" class="inline-flex items-center px-2.5 py-1 bg-orange-600 text-white rounded-lg text-[10px] font-bold hover:bg-orange-700 transition-all shadow-sm disabled:opacity-50 active:scale-95">
                                                    <span x-show="!downloading" class="flex items-center">
                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                                        Unduh Semua
                                                    </span>
                                                    <span x-show="downloading" class="flex items-center">
                                                        <svg class="animate-spin h-3 w-3 mr-1 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                                        ...
                                                    </span>
                                                </button>
                                            @endif
                                        </div>
                                        <div class="flex flex-wrap gap-2">
                                            @foreach($complaint->attachments as $file)
                                                @php 
                                                    $isImage = $file->attachment_type === 'image'; 
                                                    $previewUrl = $file->attachment_type === 'audio' 
                                                        ? route('attachments.preview', [
                                                            'account' => request()->route('account'),
                                                            'role' => request()->route('role'),
                                                            'attachment' => $file->id
                                                        ])
                                                        : asset('storage/'.$file->file_path);
                                                    
                                                    $downloadUrl = route('attachments.download', [
                                                        'account' => request()->route('account'),
                                                        'role' => request()->route('role'),
                                                        'attachment' => $file->id
                                                    ]);
                                                @endphp
                                                <div class="group relative w-16 h-16 rounded-lg overflow-hidden border border-gray-200 hover:border-orange-400 transition-colors shadow-sm bg-gray-50">
                                                    <a href="{{ $previewUrl }}" target="_blank" class="block w-full h-full">
                                                        @if($isImage)
                                                            <img src="{{ asset('storage/'.$file->file_path) }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
                                                        @else
                                                            <div class="w-full h-full flex items-center justify-center">
                                                                @if($file->attachment_type === 'video')
                                                                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                                                @elseif($file->attachment_type === 'audio')
                                                                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"></path></svg>
                                                                @else
                                                                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                                                                @endif
                                                            </div>
                                                        @endif
                                                        <div class="absolute inset-0 bg-black/0 group-hover:bg-black/5 transition-colors"></div>
                                                    </a>
                                                    <!-- Download Overlay -->
                                                    <a href="{{ $downloadUrl }}" class="absolute bottom-0 right-0 p-1 bg-white/90 rounded-tl-lg text-gray-500 hover:text-orange-600 opacity-0 group-hover:opacity-100 transition-opacity" title="Unduh">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                                    </a>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <div class="w-full lg:w-2/5" x-data="{ 
                                 decision: '{{ $complaint->status === 'rejected' ? 'rejected' : 'accepted' }}', 
                                 confirmReject: false,
                                 isProcessed: {{ $complaint->status !== 'submitted' ? 'true' : 'false' }}
                             }">
                                 <form method="POST" action="{{ route('admin.complaints.decision', $complaint) }}" class="space-y-3">
                                     @csrf
                                     <div>
                                         <x-input-label :value="'Keputusan Admin'" />
                                         <div class="mt-2 flex gap-4 text-sm">
                                             <label class="inline-flex items-center gap-2 {{ $complaint->status !== 'submitted' ? 'opacity-50' : '' }}">
                                                 <input type="radio" name="decision" value="accepted" x-model="decision" :disabled="isProcessed" class="border-gray-300 text-orange-600 focus:ring-orange-500">
                                                 Diterima
                                             </label>
                                             <label class="inline-flex items-center gap-2 {{ $complaint->status !== 'submitted' ? 'opacity-50' : '' }}">
                                                 <input type="radio" name="decision" value="rejected" x-model="decision" :disabled="isProcessed" class="border-gray-300 text-red-600 focus:ring-red-500">
                                                 Tolak
                                             </label>
                                         </div>
                                         <x-input-error :messages="$errors->get('decision')" class="mt-2" />
                                     </div>

                                     <div x-show="decision === 'accepted'" class="space-y-3">
                                         <x-text-input name="category" type="text" class="block w-full" :value="$complaint->category" placeholder="Kategori laporan" />
                                         <select name="assigned_unit_id" class="block w-full border-gray-300 focus:border-orange-500 focus:ring-orange-500 rounded-md shadow-sm" required>
                                             <option value="">Pilih instansi tujuan</option>
                                             @foreach ($units as $unit)
                                                 <option value="{{ $unit->id }}" @selected($complaint->assigned_unit_id === $unit->id)>{{ $unit->name }}</option>
                                             @endforeach
                                         </select>
                                         @php
                                             $lastInstruction = $complaint->actions()->whereIn('action_type', ['decision_accepted', 'decision_updated'])->first()?->notes;
                                         @endphp
                                         <textarea name="instruction_for_unit" rows="2" class="block w-full border-gray-300 focus:border-orange-500 focus:ring-orange-500 rounded-md shadow-sm" placeholder="Instruksi tindak lanjut ke instansi">{{ $lastInstruction }}</textarea>
                                         <x-input-error :messages="$errors->get('category')" class="mt-1" />
                                         <x-input-error :messages="$errors->get('assigned_unit_id')" class="mt-1" />
                                         <x-input-error :messages="$errors->get('instruction_for_unit')" class="mt-1" />
                                     </div>

                                     <div x-show="decision === 'rejected'" class="space-y-3">
                                         @php
                                             $lastReason = $complaint->status === 'rejected' ? $complaint->actions()->whereIn('action_type', ['decision_rejected', 'decision_updated'])->first()?->notes : '';
                                         @endphp
                                         <textarea name="rejection_reason" rows="2" class="block w-full border-gray-300 focus:border-red-500 focus:ring-red-500 rounded-md shadow-sm" placeholder="Alasan penolakan">{{ $lastReason }}</textarea>
                                         <x-input-error :messages="$errors->get('rejection_reason')" class="mt-1" />
                                     </div>

                                     <div x-show="decision === 'accepted'">
                                         @php
                                             $lastMsgCitizen = $complaint->actions()->whereIn('action_type', ['decision_accepted', 'decision_updated'])->first()?->meta['message_for_citizen'] ?? '';
                                         @endphp
                                         <textarea name="message_for_citizen" rows="2" class="block w-full border-gray-300 focus:border-orange-500 focus:ring-orange-500 rounded-md shadow-sm" placeholder="Pesan untuk masyarakat">{{ $lastMsgCitizen }}</textarea>
                                         <x-input-error :messages="$errors->get('message_for_citizen')" class="mt-1" />
                                     </div>

                                     <button x-show="decision === 'accepted'" type="submit" class="inline-flex items-center px-4 py-2 bg-orange-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase hover:bg-orange-700">
                                         {{ $complaint->status === 'submitted' ? 'Verifikasi & Teruskan' : 'Simpan Perubahan' }}
                                     </button>
                                     
                                     <div x-show="decision === 'rejected'">
                                         @if($complaint->status === 'submitted')
                                            <button type="button" @click="confirmReject = true" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase hover:bg-red-700">
                                                Tolak Laporan
                                            </button>
                                         @else
                                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase hover:bg-red-700">
                                                Simpan Perubahan
                                            </button>
                                         @endif
                                     </div>

                                     <div x-show="confirmReject" x-cloak class="rounded-md border border-red-200 bg-red-50 p-3 space-y-2">
                                         <p class="text-sm text-red-700">Yakin menolak laporan ini? Pastikan alasan penolakan sudah diisi.</p>
                                         <div class="flex gap-2">
                                             <button type="submit" class="inline-flex items-center px-3 py-2 bg-red-600 border border-transparent rounded-md text-xs font-semibold text-white uppercase hover:bg-red-700">
                                                 Ya, Tolak
                                             </button>
                                             <button type="button" @click="confirmReject = false" class="inline-flex items-center px-3 py-2 bg-white border border-gray-300 rounded-md text-xs font-semibold text-gray-700 uppercase hover:bg-gray-50">
                                                 Batal
                                             </button>
                                         </div>
                                     </div>
                                 </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="bg-white shadow-sm sm:rounded-lg p-6 text-sm text-gray-500">
                        Belum ada pengaduan untuk ditampilkan.
                    </div>
                @endforelse
            </div>

            <div>
                {{ $complaints->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
