<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Form Pengajuan Pengaduan
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm rounded-2xl overflow-hidden border border-gray-100">
                <div class="p-8">
                    <div class="mb-8">
                        <h3 class="text-xl font-bold text-gray-900">Sampaikan Laporan Anda</h3>
                        <p class="text-sm text-gray-500 mt-1">Isi form di bawah ini dengan informasi yang akurat untuk mempercepat proses tindak lanjut.</p>
                    </div>

                    <form method="POST" action="{{ route('complaints.store') }}" enctype="multipart/form-data" class="space-y-6">
                        @csrf

                        <div class="space-y-2">
                            <x-input-label for="title" :value="'Judul Singkat Laporan'" />
                            <x-text-input id="title" name="title" type="text" class="block w-full rounded-xl" :value="old('title')" placeholder="Contoh: Lampu jalan mati di Jl. Merdeka" required />
                            <x-input-error :messages="$errors->get('title')" class="mt-2" />
                        </div>

                        <div class="space-y-2">
                            <x-input-label for="description" :value="'Detail Laporan / Kronologi'" />
                            <textarea id="description" name="description" rows="5" class="block w-full border-gray-300 focus:border-orange-500 focus:ring-orange-500 rounded-xl shadow-sm placeholder-gray-400" placeholder="Ceritakan kejadian atau keluhan Anda secara lengkap..." required>{{ old('description') }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <x-input-label for="location_text" :value="'Lokasi Kejadian'" />
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                    </span>
                                    <x-text-input id="location_text" name="location_text" type="text" class="block w-full pl-10 rounded-xl" :value="old('location_text')" placeholder="Nama jalan, RT/RW, atau koordinat" />
                                </div>
                                <x-input-error :messages="$errors->get('location_text')" class="mt-2" />
                            </div>
                            <div class="space-y-2">
                                <x-input-label for="category" :value="'Kategori (Opsional)'" />
                                <x-text-input id="category" name="category" type="text" class="block w-full rounded-xl" :value="old('category')" placeholder="Misal: Lingkungan, Keamanan, dll" />
                                <x-input-error :messages="$errors->get('category')" class="mt-2" />
                            </div>
                        </div>

                        <div class="space-y-2 p-6 bg-gray-50 rounded-2xl border border-dashed border-gray-200">
                            <x-input-label for="attachments" :value="'Lampiran Pendukung'" />
                            <p class="text-xs text-gray-500 mb-3">Anda bisa mengunggah foto, dokumen, video, atau rekaman suara (maks. 10MB/file).</p>
                            <input id="attachments" name="attachments[]" type="file" multiple class="block w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:bg-orange-500 file:text-white hover:file:bg-orange-600 cursor-pointer" />
                            <x-input-error :messages="$errors->get('attachments.*')" class="mt-2" />
                        </div>

                        <div class="flex flex-col md:flex-row items-center gap-4 pt-4">
                            <x-primary-button class="w-full md:w-auto px-10 py-3 rounded-xl justify-center">
                                Kirim Laporan Sekarang
                            </x-primary-button>
                            <a href="{{ route('complaints.my') }}" class="text-sm font-bold text-gray-500 hover:text-gray-700 transition-colors">
                                Batal & Kembali
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
