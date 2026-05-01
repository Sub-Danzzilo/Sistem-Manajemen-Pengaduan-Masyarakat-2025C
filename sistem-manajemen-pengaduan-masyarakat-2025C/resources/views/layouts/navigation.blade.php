<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    @php
        $user = Auth::user();
        if ($user) {
            $notifs = $user->notifications()->latest()->limit(5)->get();
            $unreadCount = $user->unreadNotifications()->count();
            $hasNew = $unreadCount > 0;
        } else {
            $notifs = collect();
            $unreadCount = 0;
            $hasNew = false;
        }
    @endphp

    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <img src="{{ asset('images/Logo-SiMPeKat.svg') }}" class="w-16 h-16" alt="Logo">
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        Beranda
                    </x-nav-link>
 
                    @if ($user->isMasyarakat())
                        <x-nav-link :href="route('complaints.create')" :active="request()->routeIs('complaints.create')">
                            Buat Pengaduan
                        </x-nav-link>
                        <x-nav-link :href="route('complaints.my')" :active="request()->routeIs('complaints.my') || request()->routeIs('complaints.show')">
                            Pengaduan Saya
                        </x-nav-link>
                    @elseif ($user->isAdmin())
                        <x-nav-link :href="route('admin.complaints.index')" :active="request()->routeIs('admin.complaints.*')">
                            Verifikasi
                        </x-nav-link>
                    @elseif ($user->isInstansi())
                        <x-nav-link :href="route('instansi.complaints.index')" :active="request()->routeIs('instansi.complaints.*')">
                            Tindak Lanjut
                        </x-nav-link>
                    @endif
                </div>
            </div>
 
            <div class="flex items-center gap-2">
                <!-- Notifications (Always Visible) -->
                <x-dropdown align="right" width="w-80 md:w-96">
                    <x-slot name="trigger">
                        <button class="relative p-2 text-gray-400 hover:text-orange-600 transition-colors focus:outline-none">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                            @if($hasNew)
                                <span class="absolute top-2 right-2 w-2.5 h-2.5 bg-red-500 border-2 border-white rounded-full"></span>
                            @endif
                        </button>
                    </x-slot>
                    <x-slot name="content">
                        <div class="p-4 border-b border-gray-100 flex justify-between items-center">
                            <h3 class="text-xs font-bold text-gray-900 uppercase tracking-widest">Notifikasi Terbaru</h3>
                            <span class="text-[10px] text-gray-400">{{ $user->role }}</span>
                        </div>
                        <div class="max-h-80 overflow-y-auto">
                            @forelse($notifs as $n)
                                <form action="{{ route('notifications.mark-as-read', ['id' => $n->id]) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-full text-left block p-4 hover:bg-gray-50 transition-colors border-b border-gray-50 last:border-0 {{ $n->unread() ? 'bg-orange-50/30' : '' }}">
                                        <div class="flex justify-between items-start">
                                            <p class="text-xs font-bold text-gray-900 line-clamp-1">
                                                @if($n->unread())
                                                    <span class="inline-block w-2 h-2 bg-orange-500 rounded-full mr-1"></span>
                                                @endif
                                                {{ data_get($n->data, 'title') }}
                                            </p>
                                            <span class="text-[8px] px-1.5 py-0.5 rounded bg-gray-100 text-gray-500 whitespace-nowrap ml-2">{{ $n->created_at->diffForHumans(null, true) }}</span>
                                        </div>
                                        <p class="text-[10px] text-gray-500 mt-1 line-clamp-2">
                                            {{ data_get($n->data, 'message') }}
                                        </p>
                                    </button>
                                </form>
                            @empty
                                <div class="p-8 text-center text-gray-400 text-xs">
                                    Belum ada notifikasi.
                                </div>
                            @endforelse
                        </div>
                        <div class="p-2 border-t border-gray-100 text-center">
                            <a href="{{ route('notifications.index') }}" class="text-[10px] font-bold text-orange-600 hover:text-orange-700 uppercase tracking-widest">
                                Lihat Semua Notifikasi
                            </a>
                        </div>

                    </x-slot>
                </x-dropdown>

                <!-- Settings Dropdown (Desktop Only) -->
                <div class="hidden sm:flex sm:items-center">
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                                <div>{{ Auth::user()->name }}</div>
    
                                <div class="ms-1">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>
                        </x-slot>
    
                        <x-slot name="content">
                            <x-dropdown-link :href="route('profile.edit')">
                                Profil
                            </x-dropdown-link>
    
                            <!-- Authentication -->
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
    
                                <x-dropdown-link :href="route('logout')"
                                        onclick="event.preventDefault();
                                                    this.closest('form').submit();">
                                    Keluar
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                </div>

                <!-- Hamburger -->
                <div class="-me-2 flex items-center sm:hidden">
                    <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                        <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                Beranda
            </x-responsive-nav-link>

            @if ($user->isMasyarakat())
                <x-responsive-nav-link :href="route('complaints.create')" :active="request()->routeIs('complaints.create')">
                    Buat Pengaduan
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('complaints.my')" :active="request()->routeIs('complaints.my') || request()->routeIs('complaints.show')">
                    Pengaduan Saya
                </x-responsive-nav-link>
            @elseif ($user->isAdmin())
                <x-responsive-nav-link :href="route('admin.complaints.index')" :active="request()->routeIs('admin.complaints.*')">
                    Verifikasi
                </x-responsive-nav-link>
            @elseif ($user->isInstansi())
                <x-responsive-nav-link :href="route('instansi.complaints.index')" :active="request()->routeIs('instansi.complaints.*')">
                    Tindak Lanjut
                </x-responsive-nav-link>
            @endif
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    Profil
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        Keluar
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
