<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Panel Verifikasi Admin
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
                    <div class="bg-white shadow-sm sm:rounded-lg p-6">
                        <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
                            <div class="w-full lg:w-3/5">
                                <div class="flex items-center gap-2">
                                    <h3 class="text-lg font-semibold text-gray-900">{{ $complaint->title }}</h3>
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $statusClass }}">
                                        {{ $complaint->status_label }}
                                    </span>
                                </div>
                                <p class="text-sm text-gray-600 mt-1">Pelapor: {{ $complaint->reporter->name }}</p>
                                <p class="text-sm text-gray-600">Kategori: {{ $complaint->category ?: '-' }}</p>
                                <p class="text-sm text-gray-600">Lokasi: {{ $complaint->location_text ?: '-' }}</p>
                                <p class="text-sm text-gray-700 mt-3">{{ $complaint->description }}</p>
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
                                     </div>

                                     <div x-show="decision === 'accepted'" class="space-y-3">
                                         <x-text-input name="category" type="text" class="block w-full" :value="$complaint->category" placeholder="Kategori laporan" />
                                         <select name="assigned_unit_id" class="block w-full border-gray-300 focus:border-orange-500 focus:ring-orange-500 rounded-md shadow-sm">
                                             <option value="">Pilih instansi tujuan</option>
                                             @foreach ($units as $unit)
                                                 <option value="{{ $unit->id }}" @selected($complaint->assigned_unit_id === $unit->id)>{{ $unit->name }}</option>
                                             @endforeach
                                         </select>
                                         @php
                                             $lastInstruction = $complaint->actions()->whereIn('action_type', ['decision_accepted', 'decision_updated'])->first()?->notes;
                                         @endphp
                                         <textarea name="instruction_for_unit" rows="2" class="block w-full border-gray-300 focus:border-orange-500 focus:ring-orange-500 rounded-md shadow-sm" placeholder="Instruksi tindak lanjut ke instansi">{{ $lastInstruction }}</textarea>
                                     </div>

                                     <div x-show="decision === 'rejected'" class="space-y-3">
                                         @php
                                             $lastReason = $complaint->status === 'rejected' ? $complaint->actions()->whereIn('action_type', ['decision_rejected', 'decision_updated'])->first()?->notes : '';
                                         @endphp
                                         <textarea name="rejection_reason" rows="2" class="block w-full border-gray-300 focus:border-red-500 focus:ring-red-500 rounded-md shadow-sm" placeholder="Alasan penolakan">{{ $lastReason }}</textarea>
                                     </div>

                                     <div x-show="decision === 'accepted'">
                                         @php
                                             $lastMsgCitizen = $complaint->actions()->whereIn('action_type', ['decision_accepted', 'decision_updated'])->first()?->meta['message_for_citizen'] ?? '';
                                         @endphp
                                         <textarea name="message_for_citizen" rows="2" class="block w-full border-gray-300 focus:border-orange-500 focus:ring-orange-500 rounded-md shadow-sm" placeholder="Pesan untuk masyarakat">{{ $lastMsgCitizen }}</textarea>
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
