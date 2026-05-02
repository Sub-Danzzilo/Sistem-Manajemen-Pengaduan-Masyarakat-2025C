<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use App\Models\ComplaintAction;
use App\Models\ComplaintAttachment;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Models\SystemLog;

class ComplaintController extends Controller
{
    public function create(string $account, string $role): View
    {
        return view('complaints.create');
    }

    public function store(Request $request, string $account, string $role): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:5000'],
            'location_text' => ['nullable', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:100'],
            'attachments.*' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf,doc,docx,mp4,mov,avi,mp3,wav,m4a,ogg,webm'],
        ]);

        // Custom Total Size Check (50MB = 51200 KB)
        $totalSize = 0;
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $totalSize += $file->getSize();
            }
        }
        
        if ($totalSize > 51200 * 1024) {
            return back()->withErrors(['attachments' => 'Total ukuran file tidak boleh melebihi 50MB.'])->withInput();
        }

        DB::beginTransaction();

        try {
            $complaint = Complaint::create([
                'reporter_id' => $request->user()->id,
                'title' => $validated['title'],
                'description' => $validated['description'],
                'location_text' => $validated['location_text'] ?? null,
                'category' => $validated['category'] ?? null,
                'status' => Complaint::STATUS_SUBMITTED,
            ]);

            $this->logAction(
                complaint: $complaint,
                actorId: $request->user()->id,
                actionType: 'submitted',
                fromStatus: null,
                toStatus: Complaint::STATUS_SUBMITTED,
                notes: 'Pengaduan dibuat oleh masyarakat.',
            );

            $uploadedFiles = [];

            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $mime = $file->getMimeType();
                    $type = $this->resolveAttachmentType($mime);
                    
                    if ($type === ComplaintAttachment::TYPE_IMAGE) {
                        // Compress Image
                        $path = $this->compressAndStoreImage($file, "complaints/{$complaint->id}");
                    } else {
                        // Normal Store
                        $path = $file->store("complaints/{$complaint->id}", 'public');
                    }

                    $uploadedFiles[] = $path;

                    ComplaintAttachment::create([
                        'complaint_id' => $complaint->id,
                        'uploaded_by_user_id' => $request->user()->id,
                        'attachment_type' => $type,
                        'original_name' => $file->getClientOriginalName(),
                        'file_path' => $path,
                        'mime_type' => $mime ?? 'application/octet-stream',
                        'file_size' => Storage::disk('public')->size($path),
                    ]);
                }
            }

            DB::commit();
            return back()->with('status', 'Laporan pengaduan Anda berhasil terkirim dan sedang diproses.');

        } catch (\Throwable $e) {
            DB::rollBack();

            // Cleanup uploaded files if transaction fails
            if (isset($uploadedFiles)) {
                foreach ($uploadedFiles as $filePath) {
                    Storage::disk('public')->delete($filePath);
                }
            }

            SystemLog::log(
                message: 'Gagal menyimpan pengaduan: ' . $e->getMessage(),
                category: 'ComplaintController@store',
                exception: $e
            );

            return back()
                ->withErrors(['error' => 'Terjadi kesalahan saat menyimpan pengaduan. Silakan coba lagi nanti atau hubungi administrator.'])
                ->withInput();
        }
    }

    /**
     * Compress and store image using GD library
     */
    private function compressAndStoreImage($file, $folder): string
    {
        $extension = $file->getClientOriginalExtension();
        $filename = \Illuminate\Support\Str::random(40) . '.jpg'; // Always save as jpg for better compression
        $path = $folder . '/' . $filename;
        $fullPath = storage_path('app/public/' . $path);

        if (!file_exists(storage_path('app/public/' . $folder))) {
            mkdir(storage_path('app/public/' . $folder), 0755, true);
        }

        $sourcePath = $file->getRealPath();
        $info = getimagesize($sourcePath);
        
        // Create image from source
        if ($info['mime'] == 'image/jpeg') $image = imagecreatefromjpeg($sourcePath);
        elseif ($info['mime'] == 'image/gif') $image = imagecreatefromgif($sourcePath);
        elseif ($info['mime'] == 'image/png') $image = imagecreatefrompng($sourcePath);
        else {
            return $file->store($folder, 'public'); // Fallback to normal store
        }

        // Fix Orientation if EXIF data exists
        if ($info['mime'] == 'image/jpeg' && function_exists('exif_read_data')) {
            $exif = @exif_read_data($sourcePath);
            if($exif && isset($exif['Orientation'])) {
                switch($exif['Orientation']) {
                    case 3: $image = imagerotate($image, 180, 0); break;
                    case 6: $image = imagerotate($image, -90, 0); break;
                    case 8: $image = imagerotate($image, 90, 0); break;
                }
            }
        }

        // Resize if too large (Max Width 1600px)
        $width = imagesx($image);
        $height = imagesy($image);
        $maxWidth = 1600;
        
        if ($width > $maxWidth) {
            $newWidth = $maxWidth;
            $newHeight = floor($height * ($maxWidth / $width));
            $tmpImg = imagecreatetruecolor($newWidth, $newHeight);
            
            // Handle transparency for PNG/GIF if we were keeping them, but we're converting to JPG
            imagecopyresampled($tmpImg, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
            imagedestroy($image);
            $image = $tmpImg;
        }

        // Save as JPEG with 75% quality
        imagejpeg($image, $fullPath, 75);
        imagedestroy($image);

        return $path;
    }

    public function myIndex(Request $request, string $account, string $role): View
    {
        $status = $request->string('status')->toString();

        $complaints = Complaint::query()
            ->where('reporter_id', $request->user()->id)
            ->withCount('attachments')
            ->when($status !== '', fn ($query) => $query->where('status', $status))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('complaints.my-index', compact('complaints', 'status'));
    }

    public function show(string $account, string $role, Complaint $complaint, Request $request): View
    {
        $user = $request->user();
        
        $isAllowed = $user->isAdmin() 
            || $complaint->reporter_id === $user->id
            || ($user->isInstansi() && $complaint->assigned_unit_id === $user->id);

        abort_if(!$isAllowed, 403);

        $complaint->load(['attachments', 'actions.actor']);

        return view('complaints.show', compact('complaint'));
    }

    public function previewAttachment(string $account, string $role, ComplaintAttachment $attachment, Request $request)
    {
        $user = $request->user();
        $complaint = $attachment->complaint;
        
        $isAllowed = $user->isAdmin() 
            || $complaint->reporter_id === $user->id
            || ($user->isInstansi() && $complaint->assigned_unit_id === $user->id);

        abort_if(!$isAllowed, 403);

        $path = storage_path('app/public/' . $attachment->file_path);
        
        if (!file_exists($path)) {
            abort(404);
        }

        // For audio, we want to ensure it's served as inline to play in browser
        return response()->file($path, [
            'Content-Type' => $attachment->mime_type,
            'Content-Disposition' => 'inline; filename="' . $attachment->original_name . '"'
        ]);
    }

    public function downloadAttachment(string $account, string $role, ComplaintAttachment $attachment, Request $request)
    {
        $user = $request->user();
        $complaint = $attachment->complaint;
        
        $isAllowed = $user->isAdmin() 
            || $complaint->reporter_id === $user->id
            || ($user->isInstansi() && $complaint->assigned_unit_id === $user->id);

        abort_if(!$isAllowed, 403);

        $path = storage_path('app/public/' . $attachment->file_path);
        
        if (!file_exists($path)) {
            abort(404);
        }

        return response()->download($path, $attachment->original_name);
    }

    private function resolveAttachmentType(?string $mime): string
    {
        if (! $mime) {
            return ComplaintAttachment::TYPE_DOCUMENT;
        }

        if (str_starts_with($mime, 'image/')) {
            return ComplaintAttachment::TYPE_IMAGE;
        }

        if (str_starts_with($mime, 'video/')) {
            return ComplaintAttachment::TYPE_VIDEO;
        }

        if (str_starts_with($mime, 'audio/')) {
            return ComplaintAttachment::TYPE_AUDIO;
        }

        return ComplaintAttachment::TYPE_DOCUMENT;
    }

    private function logAction(
        Complaint $complaint,
        ?int $actorId,
        string $actionType,
        ?string $fromStatus,
        ?string $toStatus,
        ?string $notes = null,
        ?array $meta = null
    ): void {
        ComplaintAction::create([
            'complaint_id' => $complaint->id,
            'actor_user_id' => $actorId,
            'action_type' => $actionType,
            'from_status' => $fromStatus,
            'to_status' => $toStatus,
            'notes' => $notes,
            'meta' => $meta,
        ]);
    }
}
