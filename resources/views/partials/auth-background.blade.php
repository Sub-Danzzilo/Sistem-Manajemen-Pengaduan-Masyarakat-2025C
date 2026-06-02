{{-- Auth page background decorations (login, register, password reset, etc.) --}}
<div class="auth-background pointer-events-none" aria-hidden="true">
    {{-- Soft radial gradient wash --}}
    <div
        class="fixed inset-0 z-0"
        style="background: radial-gradient(ellipse 70% 60% at 50% 45%, rgba(255, 140, 66, 0.12) 0%, transparent 70%);"
    ></div>

    {{-- Dot grid pattern (desktop/tablet) --}}
    <div
        class="auth-dot-pattern fixed inset-0 z-0 hidden sm:block opacity-[0.04]"
        style="background-image: radial-gradient(circle, #FF8C42 1px, transparent 1px); background-size: 24px 24px;"
    ></div>

    {{-- Corner blobs (existing SVG assets) --}}
    <div
        class="decoration-blob top-0 left-0 w-80 h-80 sm:w-96 sm:h-96 lg:w-[28rem] lg:h-[28rem] opacity-20 lg:opacity-25 animate-auth-float"
        style="margin-top: -120px; margin-left: -120px; animation-delay: -2s;"
    >
        <img src="{{ asset('images/decorations/blob-organic.svg') }}" alt="" class="w-full h-full">
    </div>

    <div
        class="decoration-wave bottom-0 right-0 w-80 h-80 sm:w-96 sm:h-96 lg:w-[28rem] lg:h-[28rem] opacity-25 lg:opacity-30 animate-auth-float-alt"
        style="margin-bottom: -100px; margin-right: -120px; animation-delay: -5s;"
    >
        <img src="{{ asset('images/decorations/wave-organic.svg') }}" alt="" class="w-full h-full">
    </div>

    {{-- Flank decorations (wider screens) --}}
    <div class="hidden md:block">
        <div
            class="fixed left-[8%] top-[35%] w-40 h-40 lg:w-52 lg:h-52 rounded-full bg-orange-400/10 blur-2xl z-0 animate-auth-float"
            style="animation-delay: -7s;"
        ></div>

        <div
            class="fixed right-[10%] top-[55%] w-32 h-32 lg:w-44 lg:h-44 rounded-full bg-orange-500/10 blur-2xl z-0 animate-auth-float-alt"
            style="animation-delay: -4s;"
        ></div>

        <div
            class="fixed right-[15%] top-[18%] w-24 h-24 lg:w-32 lg:h-32 rounded-full bg-orange-300/15 blur-xl z-0 animate-auth-float"
            style="animation-delay: -9s;"
        ></div>

        <div
            class="fixed left-[12%] bottom-[20%] w-28 h-28 lg:w-36 lg:h-36 rounded-full bg-orange-400/8 blur-2xl z-0 animate-auth-float-alt"
            style="animation-delay: -1s;"
        ></div>

        <div
            class="decoration-blob left-[5%] bottom-[8%] w-32 h-32 lg:w-40 lg:h-40 opacity-15 animate-auth-float-alt"
            style="animation-delay: -6s;"
        >
            <img src="{{ asset('images/decorations/blob-organic.svg') }}" alt="" class="w-full h-full scale-75 origin-center">
        </div>

        <div
            class="decoration-wave right-[6%] top-[42%] w-28 h-28 lg:w-36 lg:h-36 opacity-15 animate-auth-float"
            style="animation-delay: -3s;"
        >
            <img src="{{ asset('images/decorations/wave-organic.svg') }}" alt="" class="w-full h-full scale-75 origin-center rotate-180">
        </div>
    </div>
</div>
