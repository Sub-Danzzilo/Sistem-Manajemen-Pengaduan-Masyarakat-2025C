<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Beranda {{ ucfirst($role) }}
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Welcome Banner -->
            <div class="bg-gradient-to-r from-orange-500 to-orange-600 rounded-2xl p-8 shadow-lg text-white">
                <div class="flex flex-col md:flex-row justify-between items-center gap-6">
                    <div>
                        <h3 class="text-2xl font-bold">Halo, {{ Auth::user()->name }}! 👋</h3>
                        <p class="mt-2 text-orange-50/90 max-w-md">
                            @if ($role === \App\Models\User::ROLE_MASYARAKAT)
                                Selamat datang di SiMPeKat. Sampaikan aspirasi dan laporan Anda untuk lingkungan yang lebih baik.
                            @elseif ($role === \App\Models\User::ROLE_ADMIN)
                                Selamat bertugas. Pantau, verifikasi, dan kelola setiap laporan masyarakat dengan cepat dan tepat.
                            @else
                                Selamat bekerja. Berikan pelayanan terbaik dengan menindaklanjuti setiap pengaduan yang ditugaskan.
                            @endif
                        </p>
                        <div class="mt-6 flex flex-wrap gap-3">
                            @if ($role === \App\Models\User::ROLE_MASYARAKAT)
                                <a href="{{ route('complaints.create') }}" class="inline-flex items-center px-6 py-3 bg-white text-orange-600 rounded-xl font-bold text-sm shadow-sm hover:bg-orange-50 transition-colors">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                    Buat Pengaduan
                                </a>
                            @elseif ($role === \App\Models\User::ROLE_ADMIN)
                                <a href="{{ route('admin.complaints.index') }}" class="inline-flex items-center px-6 py-3 bg-white text-orange-600 rounded-xl font-bold text-sm shadow-sm hover:bg-orange-50 transition-colors">
                                    Panel Verifikasi
                                </a>
                            @else
                                <a href="{{ route('instansi.complaints.index') }}" class="inline-flex items-center px-6 py-3 bg-white text-orange-600 rounded-xl font-bold text-sm shadow-sm hover:bg-orange-50 transition-colors">
                                    Panel Tindak Lanjut
                                </a>
                            @endif
                        </div>
                    </div>
                    <div class="hidden lg:block">
                        <svg class="w-48 h-48 text-orange-400/30" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm-1-13h2v6h-2zm0 8h2v2h-2z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4">
                    <div class="p-3 bg-blue-50 text-blue-600 rounded-xl">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Total Laporan</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</p>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4">
                    <div class="p-3 bg-yellow-50 text-yellow-600 rounded-xl">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Menunggu</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['submitted'] }}</p>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4">
                    <div class="p-3 bg-indigo-50 text-indigo-600 rounded-xl">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Diproses</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['in_progress'] }}</p>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4">
                    <div class="p-3 bg-emerald-50 text-emerald-600 rounded-xl">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Selesai</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['resolved'] }}</p>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <h3 class="font-bold text-gray-900">Laporan Terbaru</h3>
                    <div class="flex items-center gap-3 w-full sm:w-auto">
                        <form method="GET" class="flex-1 sm:flex-none">
                            <select name="status" onchange="this.form.submit()" class="text-xs border-gray-200 focus:border-orange-500 focus:ring-orange-500 rounded-lg bg-gray-50">
                                <option value="">Semua Status</option>
                                <option value="submitted" @selected(($statusFilter ?? '') === 'submitted')>Menunggu</option>
                                <option value="assigned" @selected(($statusFilter ?? '') === 'assigned')>Diteruskan</option>
                                <option value="in_progress" @selected(($statusFilter ?? '') === 'in_progress')>Diproses</option>
                                <option value="resolved" @selected(($statusFilter ?? '') === 'resolved')>Selesai</option>
                                <option value="rejected" @selected(($statusFilter ?? '') === 'rejected')>Ditolak</option>
                            </select>
                        </form>
                        @php
                            $seeAllRoute = match($role) {
                                \App\Models\User::ROLE_ADMIN => route('admin.complaints.index'),
                                \App\Models\User::ROLE_INSTANSI => route('instansi.complaints.index'),
                                default => route('complaints.my'),
                            };
                        @endphp
                        <a href="{{ $seeAllRoute }}" class="text-sm text-orange-600 font-semibold hover:underline whitespace-nowrap">Lihat Semua</a>
                    </div>
                </div>
                <div class="divide-y divide-gray-100">
                    @forelse ($recentComplaints as $item)
                        @php
                            $statusClass = match ($item->status) {
                                'submitted' => 'bg-yellow-50 text-yellow-700 border-yellow-100',
                                'verified', 'assigned', 'in_progress' => 'bg-blue-50 text-blue-700 border-blue-100',
                                'resolved' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                                'rejected' => 'bg-red-50 text-red-700 border-red-100',
                                default => 'bg-gray-50 text-gray-700 border-gray-100',
                            };
                        @endphp
                        <div class="p-6 hover:bg-gray-50 transition-colors flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div>
                                <p class="font-bold text-gray-900">{{ $item->title }}</p>
                                <p class="text-xs text-gray-500 mt-1">{{ $item->created_at->diffForHumans() }}</p>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider border {{ $statusClass }}">
                                    {{ $item->status_label }}
                                </span>
                                <a href="{{ route('complaints.show', $item) }}" class="p-2 text-gray-400 hover:text-orange-600">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="p-10 text-center text-gray-500">
                            <p>Belum ada laporan yang dikirim.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
