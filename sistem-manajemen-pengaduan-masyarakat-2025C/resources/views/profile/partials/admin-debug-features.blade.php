<section class="space-y-6">
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            Debug Tools
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('Fitur khusus untuk keperluan pengembangan dan testing.') }}
        </p>
    </header>

    <div class="p-4 bg-red-50 border border-red-200 rounded-md">
        <h3 class="text-md font-semibold text-red-800 mb-2">Hapus Seluruh Data Laporan</h3>
        <p class="text-sm text-red-600 mb-4">
            Tindakan ini akan menghapus semua pengaduan, riwayat aksi, dan lampiran secara permanen dari database.
        </p>

        <x-danger-button
            x-data=""
            x-on:click.prevent="$dispatch('open-modal', 'confirm-delete-all-complaints')"
        >
            {{ __('Hapus Semua Laporan') }}
        </x-danger-button>
    </div>

    <x-modal name="confirm-delete-all-complaints" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('admin.debug.delete-all-complaints') }}" class="p-6">
            @csrf

            <h2 class="text-lg font-medium text-gray-900">
                {{ __('Apakah Anda yakin ingin menghapus seluruh data laporan?') }}
            </h2>

            <p class="mt-1 text-sm text-gray-600">
                {{ __('Semua data pengaduan dari seluruh masyarakat dan instansi akan dihapus permanen. Tindakan ini tidak dapat dibatalkan.') }}
            </p>

            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('Batal') }}
                </x-secondary-button>

                <x-danger-button class="ms-3">
                    {{ __('Ya, Hapus Semua') }}
                </x-danger-button>
            </div>
        </form>
    </x-modal>
</section>
