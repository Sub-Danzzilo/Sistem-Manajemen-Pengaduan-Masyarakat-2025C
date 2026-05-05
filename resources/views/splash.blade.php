<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'SiMPeKat') }} - Selamat Datang</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            /* 
             * PANDUAN MENGUBAH UKURAN AWAL LOGO
             * Anda bisa mengubah nilai variabel '--logo-base-width' di bawah ini
             * untuk menyesuaikan ukuran logo. Secara default, ukurannya adalah 160px (w-40 di Tailwind).
             */
            :root {
                /* Ubah angka ini untuk memperbesar/memperkecil logo */
                --logo-base-width: 300px;
            }

            .splash-logo {
                width: var(--logo-base-width);
                height: auto;
                opacity: 0;
                /* Animasi total 3 detik, berjalan 1 kali (forwards agar tertahan di frame terakhir) */
                animation: splashAnim 3s ease-in-out forwards;
            }

            @keyframes splashAnim {
                0% {
                    opacity: 0;
                    transform: scale(0.8);
                }
                20% {
                    opacity: 1;
                    transform: scale(1.1); /* Growth pelan */
                }
                80% {
                    opacity: 1;
                    transform: scale(0.9); /* Shrink pelan */
                }
                100% {
                    opacity: 0;
                    transform: scale(0.8); /* Fade out dan kembali mengecil */
                }
            }
        </style>
    </head>
    <body class="font-sans text-gray-900 antialiased min-h-screen flex items-center justify-center bg-gray-50 pointer-events-none select-none">
        
        <!-- Logo Container -->
        <div class="flex items-center justify-center w-full h-full">
            <img src="{{ asset('images/Logo-SiMPeKat.svg') }}" alt="Logo SiMPeKat" class="splash-logo" />
        </div>

        <!-- Skip Button khusus untuk Admin (Debug Mode) -->
        @if($debug)
            <div class="fixed bottom-4 right-4 sm:bottom-6 sm:right-6 pointer-events-auto z-50">
                <a href="{{ route('profile.edit') }}#debug-tools" 
                   class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-lg">
                    Skip Animasi
                </a>
            </div>
        @endif

        <script>
            // Setelah 3 detik (3000ms), redirect ke halaman yang sesuai
            setTimeout(() => {
                @if($debug)
                    window.location.href = "{{ route('profile.edit') }}#debug-tools";
                @else
                    window.location.href = "{{ route('login') }}";
                @endif
            }, 3000);
        </script>
    </body>
</html>
