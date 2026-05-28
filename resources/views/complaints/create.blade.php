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


                    <form method="POST" action="{{ route('complaints.store') }}" enctype="multipart/form-data" class="space-y-6" 
                        x-data="fileManager()" 
                        @submit="submitForm">
                        @csrf

                        <div class="space-y-2">
                            <x-input-label for="title" :value="'Judul Singkat Laporan'" />
                            <x-text-input id="title" name="title" type="text" class="block w-full rounded-xl" :value="old('title')" placeholder="Contoh: Lampu jalan mati di Jl. Merdeka" required />
                        </div>

                        <div class="space-y-2">
                            <x-input-label for="description" :value="'Detail Laporan / Kronologi'" />
                            <textarea id="description" name="description" rows="5" class="block w-full border-gray-300 focus:border-orange-500 focus:ring-orange-500 rounded-xl shadow-sm placeholder-gray-400" placeholder="Ceritakan kejadian atau keluhan Anda secara lengkap..." required>{{ old('description') }}</textarea>
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
                            </div>
                            <div class="space-y-2">
                                <x-input-label for="category" :value="'Kategori (Opsional)'" />
                                <x-text-input id="category" name="category" type="text" class="block w-full rounded-xl" :value="old('category')" placeholder="Misal: Lingkungan, Keamanan, dll" />
                            </div>
                        </div>

                        <!-- Multi-File Upload Section -->
                        <div class="space-y-4 p-6 bg-gray-50 rounded-2xl border border-dashed border-gray-200">
                            <div>
                                <x-input-label :value="'Lampiran Pendukung'" />
                                <p class="text-xs text-gray-500 mb-3">Pilih foto, video, atau dokumen (Total maks. 50MB).</p>
                                
                                <div class="flex items-center gap-2">
                                    <label class="cursor-pointer inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-xl font-bold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 transition-colors">
                                        <svg class="w-4 h-4 mr-2 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                        Pilih File
                                        <input type="file" @change="addFiles($event.target.files)" multiple class="hidden" accept="image/*,video/*,audio/*,.pdf,.doc,.docx">
                                    </label>
                                    <span x-text="`${files.length} file dipilih`" class="text-xs text-gray-400"></span>
                                </div>
                            </div>

                            <!-- Total Size Warning -->
                            <div x-show="totalSize > 0" class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <div class="h-2 w-48 bg-gray-200 rounded-full overflow-hidden">
                                        <div class="h-full transition-all duration-300" 
                                             :class="totalSize > 51200 ? 'bg-red-500' : 'bg-orange-500'"
                                             :style="`width: ${Math.min((totalSize/51200)*100, 100)}%`"
                                        ></div>
                                    </div>
                                    <span class="text-[10px] font-bold" :class="totalSize > 51200 ? 'text-red-500' : 'text-gray-500'">
                                        <span x-text="(totalSize/1024).toFixed(2)"></span> MB / 50 MB
                                    </span>
                                </div>
                            </div>

                            <!-- Files List -->
                            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4" x-show="files.length > 0">
                                <template x-for="(file, index) in files" :key="index">
                                    <div class="group relative flex flex-col bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden transition-all hover:shadow-md">
                                        <!-- Preview/Icon Area -->
                                        <div class="aspect-video relative bg-gray-100 flex items-center justify-center">
                                            <!-- Image Preview -->
                                            <template x-if="file.type.startsWith('image/') && file.preview">
                                                <img :src="file.preview" class="w-full h-full object-cover">
                                            </template>
                                            
                                            <!-- Icons for Video/Audio/Docs -->
                                            <template x-if="!file.type.startsWith('image/') || !file.preview">
                                                <div class="flex flex-col items-center justify-center text-gray-400">
                                                    <svg x-show="file.type.startsWith('video/')" class="w-10 h-10 text-orange-500/50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                                    <svg x-show="file.type.startsWith('audio/')" class="w-10 h-10 text-emerald-500/50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"></path></svg>
                                                    <svg x-show="!file.type.startsWith('video/') && !file.type.startsWith('audio/')" class="w-10 h-10 text-blue-500/50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                                                </div>
                                            </template>

                                            <!-- Delete Button (Accessible for Mobile) -->
                                            <button @click.prevent="removeFile(index)" class="absolute top-2 right-2 p-2 bg-red-500 hover:bg-red-600 text-white rounded-xl shadow-lg transition-colors z-10">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                            </button>
                                        </div>

                                        <!-- File Information Area -->
                                        <div class="p-3 border-t border-gray-50">
                                            <p x-text="file.name" class="text-[11px] font-bold text-gray-800 truncate mb-1"></p>
                                            <p x-text="formatSize(file.size)" class="text-[9px] text-gray-500 font-medium"></p>
                                        </div>
                                    </div>
                                </template>
                            </div>

                            <div x-show="totalSize > 51200" class="text-xs text-red-500 font-bold">Total file melebihi batas 50MB. Harap kurangi file.</div>
                        </div>

                        <!-- Real File Input (Hidden) -->
                        <input type="file" name="attachments[]" x-ref="finalInput" class="hidden" multiple>

                        <div class="flex flex-col md:flex-row items-center gap-4 pt-4">
                            <x-primary-button type="button" @click="openConfirmation" class="w-full md:w-auto px-10 py-3 rounded-xl justify-center disabled:opacity-75" 
                                x-bind:disabled="submitting || totalSize > 51200">
                                <span x-show="!submitting">Kirim Laporan Sekarang</span>
                                <span x-show="submitting" class="flex items-center gap-2">
                                    <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                    Sedang Memproses...
                                </span>
                            </x-primary-button>
                            <a href="{{ route('complaints.my') }}" x-show="!submitting" class="text-sm font-bold text-gray-500 hover:text-gray-700 transition-colors">
                                Lihat Daftar Laporan
                            </a>
                        </div>

                        <x-confirm-modal 
                            name="confirm-submit-complaint"
                            title="Konfirmasi Pengiriman Laporan"
                            description="Apakah Anda yakin data yang Anda masukkan sudah benar? Laporan yang sudah dikirim akan segera diproses oleh admin."
                            confirmText="Ya, Kirim Sekarang"
                            cancelText="Batal"
                            confirmType="primary" />
                    </form>

                    <script>
                        function fileManager() {
                            return {
                                files: [],
                                submitting: false,
                                totalSize: 0, // in KB
                                init() {
                                    this.syncInput();
                                },

                                syncInput() {
                                    const dataTransfer = new DataTransfer();
                                    this.files.forEach(file => dataTransfer.items.add(file));
                                    this.$refs.finalInput.files = dataTransfer.files;
                                    this.calculateTotalSize();
                                },

                                addFiles(newFiles) {
                                    for (let i = 0; i < newFiles.length; i++) {
                                        const file = newFiles[i];
                                        
                                        // Add preview only for images
                                        if (file.type.startsWith('image/')) {
                                            file.preview = URL.createObjectURL(file);
                                        } else {
                                            file.preview = null;
                                        }

                                        this.files.push(file);
                                    }
                                    this.syncInput();
                                },

                                removeFile(index) {
                                    const file = this.files[index];
                                    if (file.preview) {
                                        URL.revokeObjectURL(file.preview);
                                    }
                                    this.files.splice(index, 1);
                                    this.syncInput();
                                },

                                calculateTotalSize() {
                                    this.totalSize = this.files.reduce((acc, file) => acc + (file.size / 1024), 0);
                                },

                                formatSize(size) {
                                    if (size < 1024 * 1024) return (size / 1024).toFixed(1) + ' KB';
                                    return (size / (1024 * 1024)).toFixed(1) + ' MB';
                                },

                                openConfirmation() {
                                    if (this.totalSize > 51200) return;
                                    if (!this.$el.checkValidity()) {
                                        this.$el.reportValidity();
                                        return;
                                    }
                                    this.$dispatch('open-modal', 'confirm-submit-complaint');
                                },

                                submitForm() {
                                    this.submitting = true;
                                    this.syncInput();
                                }
                            }
                        }
                    </script>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
