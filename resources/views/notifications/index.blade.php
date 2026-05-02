<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Notifikasi
            </h2>
            @if($notifications->isNotEmpty())
                <form action="{{ route('notifications.clear-all') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus semua notifikasi?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-xs font-bold text-red-600 hover:text-red-700 uppercase tracking-widest bg-red-50 px-3 py-2 rounded-md transition-colors">
                        Bersihkan Semua
                    </button>
                </form>
            @endif
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <div class="divide-y divide-gray-100">
                    @forelse ($notifications as $n)
                        <div class="p-6 transition-colors {{ $n->unread() ? 'bg-orange-50/20' : 'bg-white' }} hover:bg-gray-50/80 group">
                            <div class="flex gap-4">
                                <!-- Status Indicator -->
                                <div class="mt-1.5 flex-shrink-0">
                                    <div class="w-3 h-3 rounded-full {{ $n->unread() ? 'bg-orange-500' : 'bg-gray-200' }}"></div>
                                </div>
                                
                                <!-- Content -->
                                <div class="flex-1">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <form action="{{ route('notifications.mark-as-read', ['id' => $n->id]) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="text-left group/title">
                                                    <h3 class="text-base font-bold text-gray-900 group-hover/title:text-orange-600 transition-colors">
                                                        {{ data_get($n->data, 'title') }}
                                                    </h3>
                                                </button>
                                            </form>
                                            <p class="text-sm text-gray-600 mt-1 leading-relaxed">
                                                {{ data_get($n->data, 'message') }}
                                            </p>
                                        </div>
                                        
                                        <!-- Actions -->
                                        <div class="ml-4 flex items-center gap-3">
                                            <span class="text-xs text-gray-400 whitespace-nowrap">
                                                {{ $n->created_at->diffForHumans() }}
                                            </span>
                                            
                                            <form action="{{ route('notifications.destroy', ['id' => $n->id]) }}" method="POST" class="opacity-0 group-hover:opacity-100 transition-opacity">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-1.5 text-gray-400 hover:text-red-600 rounded-full hover:bg-red-50 transition-colors" title="Hapus Notifikasi">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-4v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                    
                                    <div class="mt-4 flex items-center gap-4">
                                        <form action="{{ route('notifications.mark-as-read', ['id' => $n->id]) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="text-xs font-bold text-orange-600 hover:text-orange-700 underline underline-offset-4">
                                                Lihat Selengkapnya
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-20 text-center">
                            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-50 mb-4 text-gray-300">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900">Belum ada notifikasi</h3>
                            <p class="text-gray-500 mt-1">Kami akan memberi tahu Anda jika ada pembaruan pada laporan.</p>
                        </div>
                    @endforelse
                </div>

                @if($notifications->hasPages())
                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
                        {{ $notifications->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
