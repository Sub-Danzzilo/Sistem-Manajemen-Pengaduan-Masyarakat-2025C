<button {{ $attributes->merge(['type' => 'submit', 'class' => 'w-full flex justify-center items-center px-6 py-3 bg-gradient-to-r from-orange-500 to-orange-600 border border-transparent rounded-xl font-bold text-sm text-white uppercase tracking-widest hover:from-orange-600 hover:to-orange-700 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 transition-all duration-200 shadow-md hover:shadow-lg active:scale-[0.98]']) }}>
    {{ $slot }}
</button>
