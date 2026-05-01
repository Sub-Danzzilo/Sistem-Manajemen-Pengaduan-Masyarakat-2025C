<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Detail Pengaduan
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Header & Action Buttons -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                @php
                    $user = Auth::user();
                    $backRoute = match(true) {
                        $user->isAdmin() => route('admin.complaints.index'),
                        $user->isInstansi() => route('instansi.complaints.index'),
                        default => route('complaints.my'),
                    };
                @endphp
                <a href="{{ $backRoute }}#complaint-{{ $complaint->id }}" class="inline-flex items-center text-sm font-bold text-gray-500 hover:text-orange-600 transition-colors">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                    Kembali ke Daftar
                </a>
                <div class="flex items-center gap-2">
                    @php
                        $statusClass = match ($complaint->status) {
                            'submitted' => 'bg-yellow-50 text-yellow-700 border-yellow-200',
                            'verified', 'assigned', 'in_progress' => 'bg-blue-50 text-blue-700 border-blue-200',
                            'resolved' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                            'rejected' => 'bg-red-50 text-red-700 border-red-200',
                            default => 'bg-gray-50 text-gray-700 border-gray-200',
                        };
                    @endphp
                    <span class="px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-widest border {{ $statusClass }}">
                        Status: {{ $complaint->status_label }}
                    </span>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="p-8">
                            <h3 class="text-2xl font-bold text-gray-900 mb-4">{{ $complaint->title }}</h3>
                            
                            <div class="flex flex-wrap gap-6 mb-8 text-sm text-gray-500">
                                <div class="flex items-center gap-2">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                    {{ $complaint->location_text ?: 'Lokasi tidak spesifik' }}
                                </div>
                                <div class="flex items-center gap-2">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                                    {{ $complaint->category ?: 'Tanpa Kategori' }}
                                </div>
                            </div>

                            <div class="prose max-w-none text-gray-700 leading-relaxed bg-gray-50 p-6 rounded-2xl border border-gray-100">
                                {{ $complaint->description }}
                            </div>
                        </div>
                    </div>

                    <!-- Attachments -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                        <h4 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2">
                            <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                            Lampiran Pendukung
                        </h4>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            @forelse ($complaint->attachments as $attachment)
                                @php
                                    $previewUrl = $attachment->attachment_type === 'audio' 
                                        ? route('attachments.preview', [
                                            'account' => request()->route('account'),
                                            'role' => request()->route('role'),
                                            'attachment' => $attachment->id
                                        ])
                                        : asset('storage/'.$attachment->file_path);
                                @endphp
                                <a href="{{ $previewUrl }}" target="_blank" class="flex items-center p-4 bg-gray-50 rounded-xl border border-gray-100 hover:border-orange-200 transition-colors group">
                                    <div class="p-3 bg-white rounded-lg shadow-sm mr-4 text-orange-500 group-hover:bg-orange-50 transition-colors">
                                        @if($attachment->attachment_type === 'image')
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        @elseif($attachment->attachment_type === 'video')
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                        @elseif($attachment->attachment_type === 'audio')
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"></path></svg>
                                        @else
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                        @endif
                                    </div>
                                    <div class="overflow-hidden">
                                        <p class="text-sm font-bold text-gray-900 truncate">{{ $attachment->original_name }}</p>
                                        <p class="text-xs text-gray-500 uppercase">{{ $attachment->attachment_type }}</p>
                                    </div>
                                </a>
                            @empty
                                <p class="col-span-full text-sm text-gray-500 italic bg-gray-50 p-4 rounded-xl text-center">Tidak ada lampiran pendukung.</p>
                            @endforelse
                        </div>
                    </div>

                    <!-- Pesan & Instruksi Khusus -->
                    @php
                        $user = Auth::user();
                        $specialMessages = $complaint->actions->filter(function($action) use ($user) {
                            if (empty($action->notes) && empty($action->meta['message_for_citizen'])) return false;
                            
                            $type = $action->action_type;
                            if ($user->isAdmin()) return true;
                            
                            if ($user->isMasyarakat()) {
                                // Masyarakat sees decisions, progress updates, and resolutions
                                return str_starts_with($type, 'decision_') || str_starts_with($type, 'progress') || str_starts_with($type, 'in_progress') || str_starts_with($type, 'resolve');
                            }
                            
                            if ($user->isInstansi()) {
                                // Instansi sees the assignment instructions, progress updates, and resolutions
                                return $type === 'assigned' || str_starts_with($type, 'decision_') || str_starts_with($type, 'progress') || str_starts_with($type, 'in_progress') || str_starts_with($type, 'resolve');
                            }
                            return false;
                        });
                    @endphp

                    @if($specialMessages->count() > 0)
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                        <h4 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2">
                            <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
                            Catatan & Instruksi
                        </h4>
                        <div class="space-y-4">
                            @foreach($specialMessages as $msg)
                                @php
                                    $isFromAdmin = $msg->actor?->isAdmin() ?? false;
                                    $isFromInstansi = $msg->actor?->isInstansi() ?? false;
                                    
                                    $bgColor = $isFromAdmin ? 'bg-orange-50' : ($isFromInstansi ? 'bg-emerald-50' : 'bg-gray-50');
                                    $borderColor = $isFromAdmin ? 'border-orange-100' : ($isFromInstansi ? 'border-emerald-100' : 'border-gray-100');
                                    $textColor = $isFromAdmin ? 'text-orange-800' : ($isFromInstansi ? 'text-emerald-800' : 'text-gray-800');
                                    $roleName = $msg->actor ? ucfirst($msg->actor->role) : 'Sistem';

                                    // Role-based Content Selection
                                    $displayText = $msg->notes;
                                    
                                    if ($user->isMasyarakat()) {
                                        // Show citizen message if it's an admin decision
                                        if (str_starts_with($msg->action_type, 'decision_')) {
                                            $displayText = data_get($msg->meta, 'message_for_citizen', $msg->notes);
                                        }
                                    } elseif ($user->isInstansi()) {
                                        // Show instructions if it's an admin decision
                                        if (str_starts_with($msg->action_type, 'decision_')) {
                                            $displayText = data_get($msg->meta, 'instruction_for_unit', $msg->notes);
                                        }
                                    } elseif ($user->isAdmin()) {
                                        // Admin sees both if it's a decision
                                        if (str_starts_with($msg->action_type, 'decision_')) {
                                            $citizenMsg = data_get($msg->meta, 'message_for_citizen');
                                            $unitInst = data_get($msg->meta, 'instruction_for_unit');
                                            
                                            $displayText = "";
                                            if ($citizenMsg) $displayText .= "📢 [Ke Masyarakat]: " . $citizenMsg . "\n\n";
                                            if ($unitInst) $displayText .= "🏢 [Ke Instansi]: " . $unitInst;
                                            
                                            if (empty($displayText)) $displayText = $msg->notes;
                                        }
                                    }
                                @endphp
                                <div class="p-4 rounded-xl border {{ $bgColor }} {{ $borderColor }}">
                                    <div class="flex justify-between items-start mb-2">
                                        <span class="text-xs font-bold {{ $textColor }}">{{ $roleName }} {{ $msg->actor ? '('.$msg->actor->name.')' : '' }}</span>
                                        <span class="text-[10px] text-gray-500">{{ $msg->created_at->format('d M Y, H:i') }}</span>
                                    </div>
                                    <p class="text-sm text-gray-700 whitespace-pre-line font-medium">{{ $displayText }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Timeline / Stepper -->
                <div class="space-y-6">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                        <h4 class="text-lg font-bold text-gray-900 mb-8 flex items-center gap-2">
                            <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Riwayat Laporan
                        </h4>
                        
                        <div class="relative space-y-8 before:absolute before:inset-0 before:ml-5 before:-translate-x-px before:h-full before:w-0.5 before:bg-gradient-to-b before:from-orange-500 before:via-gray-200 before:to-gray-200">
                            @forelse ($complaint->actions as $action)
                                <div class="relative flex items-start gap-6 group">
                                    <div class="absolute left-0 flex items-center justify-center w-10 h-10 rounded-full bg-white border-2 border-orange-500 shadow-sm transition-transform group-hover:scale-110">
                                        <div class="w-2 h-2 rounded-full bg-orange-500"></div>
                                    </div>
                                    <div class="ml-12 pt-1">
                                        <time class="block mb-1 text-[10px] font-bold uppercase tracking-widest text-orange-600">
                                            {{ $action->created_at->format('d M Y, H:i') }}
                                        </time>
                                        <h5 class="font-bold text-gray-900 text-sm">
                                            {{ $action->action_label }}
                                        </h5>
                                        <p class="mt-1 text-xs text-gray-400">Oleh: {{ $action->actor?->name ?: 'Sistem' }}</p>
                                    </div>
                                </div>
                            @empty
                                <p class="text-sm text-gray-500 italic text-center ml-12">Belum ada pembaruan status.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
