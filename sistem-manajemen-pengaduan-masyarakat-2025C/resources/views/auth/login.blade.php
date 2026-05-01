<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Kata Sandi')" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
            <x-password-toggle-input
                id="password"
                name="password"
                required
                autocomplete="current-password"
            />
        </div>

        <!-- Remember Me & Forgot Password -->
        <div class="flex items-center justify-between mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-orange-600 shadow-sm focus:ring-orange-500" name="remember">
                <span class="ms-2 text-sm text-gray-600">{{ __('Ingatkan aku') }}</span>
            </label>

            @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500" href="{{ route('password.request') }}">
                    {{ __('Lupa kata sandi?') }}
                </a>
            @endif
        </div>

        <div class="mt-8 space-y-6">
            <x-primary-orange-button>
                Masuk
            </x-primary-orange-button>

            {{-- To Register page --}}
            @if (Route::has('register'))
                <div class="text-center text-sm text-gray-600 pt-2">
                    Belum punya akun? 
                    <a href="{{ route('register') }}" class="font-bold text-orange-600 hover:text-orange-700 transition-colors">
                        Daftar Sekarang
                    </a>
                </div>
            @endif
        </div>
    </form>

    @if(env('ADMIN_DEBUG_ENABLED'))
        <div class="mt-8 pt-6 border-t border-gray-100">
            <div class="flex items-center gap-2 mb-4">
                <div class="w-2 h-2 bg-red-500 animate-pulse rounded-full"></div>
                <h3 class="text-xs font-bold text-gray-500 uppercase tracking-widest">Debug Mode: Quick Login</h3>
            </div>
            
            <div class="grid grid-cols-2 gap-3">
                @foreach(\Database\Seeders\DatabaseSeeder::$debugAccounts as $acc)
                    <button type="button" 
                        onclick="fillLogin('{{ $acc['email'] }}', '{{ $acc['pass'] ?? $acc['password'] }}')"
                        class="flex flex-col items-start p-3 rounded-xl border {{ $acc['color'] ?? 'bg-gray-50 text-gray-700 border-gray-100' }} hover:opacity-80 transition-all text-left">
                        <span class="text-[10px] font-bold uppercase">{{ $acc['label'] ?? $acc['name'] }}</span>
                        <span class="text-[9px] mt-1 opacity-70 truncate w-full">{{ $acc['email'] }}</span>
                    </button>
                @endforeach
            </div>

            <script>
                function fillLogin(email, password) {
                    document.getElementById('email').value = email;
                    document.getElementById('password').value = password;
                    
                    // Trigger input event for any reactive listeners
                    document.getElementById('email').dispatchEvent(new Event('input'));
                    document.getElementById('password').dispatchEvent(new Event('input'));
                }
            </script>
        </div>
    @endif
</x-guest-layout>
