<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Pengaduan Saya
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Riwayat Pengaduan</h3>
                    <p class="text-sm text-gray-500">Daftar aspirasi dan laporan yang pernah Anda kirimkan.</p>
                </div>
                <a href="{{ route('complaints.create') }}" class="inline-flex items-center px-6 py-3 bg-orange-600 text-white rounded-xl font-bold text-sm shadow-sm hover:bg-orange-700 transition-colors w-full md:w-auto justify-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Buat Laporan Baru
                </a>
            </div>

            <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                <form method="GET" action="{{ route('complaints.my') }}" class="flex flex-col sm:flex-row items-end gap-4">
                    <div class="w-full sm:w-64">
                        <x-input-label for="status" :value="'Filter Status Laporan'" />
                        <select id="status" name="status" onchange="this.form.submit()" class="mt-1 block w-full border-gray-200 focus:border-orange-500 focus:ring-orange-500 rounded-xl bg-gray-50 text-sm">
                            <option value="">Semua Status</option>
                            <option value="submitted" @selected(($status ?? '') === 'submitted')>Menunggu Verifikasi</option>
                            <option value="verified" @selected(($status ?? '') === 'verified')>Diverifikasi</option>
                            <option value="assigned" @selected(($status ?? '') === 'assigned')>Ditugaskan</option>
                            <option value="in_progress" @selected(($status ?? '') === 'in_progress')>Diproses</option>
                            <option value="resolved" @selected(($status ?? '') === 'resolved')>Selesai</option>
                            <option value="rejected" @selected(($status ?? '') === 'rejected')>Ditolak</option>
                        </select>
                    </div>
                </form>
            </div>

            @if (session('status'))
                <div class="bg-green-50 text-green-700 px-4 py-3 rounded-xl border border-green-200">
                    {{ session('status') }}
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse ($complaints as $complaint)
                    @php
                        $statusClass = match ($complaint->status) {
                            'submitted' => 'bg-yellow-50 text-yellow-700 border-yellow-100',
                            'verified', 'assigned', 'in_progress' => 'bg-blue-50 text-blue-700 border-blue-100',
                            'resolved' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                            'rejected' => 'bg-red-50 text-red-700 border-red-100',
                            default => 'bg-gray-50 text-gray-700 border-gray-100',
                        };
                    @endphp
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col justify-between hover:shadow-md transition-shadow">
                        <div>
                            <div class="flex justify-between items-start mb-4">
                                <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider border {{ $statusClass }}">
                                    {{ $complaint->status_label }}
                                </span>
                                <span class="text-xs text-gray-400">{{ $complaint->created_at->format('d M Y') }}</span>
                            </div>
                            <h4 class="font-bold text-gray-900 text-lg mb-2 line-clamp-1">{{ $complaint->title }}</h4>
                            <p class="text-sm text-gray-600 line-clamp-3 mb-4">{{ $complaint->description }}</p>
                        </div>
                        
                        <div class="pt-4 border-t border-gray-50 flex items-center justify-between">
                            <div class="flex items-center gap-4 text-xs text-gray-400">
                                <span class="flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                                    {{ $complaint->attachments_count }}
                                </span>
                                <span class="flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                    {{ $complaint->location_text ? 'Ada Lokasi' : 'Tanpa Lokasi' }}
                                </span>
                            </div>
                            <a href="{{ route('complaints.show', $complaint) }}" class="text-sm font-bold text-orange-600 hover:text-orange-700">Detail &rarr;</a>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-20 text-center bg-white rounded-2xl border-2 border-dashed border-gray-100">
                        <svg class="w-16 h-16 text-gray-200 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 9.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <p class="text-gray-500 font-medium">Belum ada pengaduan yang dibuat.</p>
                        <a href="{{ route('complaints.create') }}" class="text-orange-600 font-bold hover:underline mt-2 inline-block">Mulai buat laporan pertama Anda</a>
                    </div>
                @endforelse
            </div>

            <div class="mt-8">
                {{ $complaints->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
