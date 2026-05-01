<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use App\Models\ComplaintAction;
use App\Models\ComplaintAttachment;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ComplaintController extends Controller
{
    public function create(): View
    {
        return view('complaints.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:5000'],
            'location_text' => ['nullable', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:100'],
            'attachments.*' => ['nullable', 'file', 'max:10240', 'mimes:jpg,jpeg,png,pdf,doc,docx,mp4,mov,avi,mp3,wav,m4a,ogg,webm'],
        ]);

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

        foreach ($request->file('attachments', []) as $file) {
            $path = $file->store("complaints/{$complaint->id}", 'public');

            ComplaintAttachment::create([
                'complaint_id' => $complaint->id,
                'uploaded_by_user_id' => $request->user()->id,
                'attachment_type' => $this->resolveAttachmentType($file->getMimeType()),
                'original_name' => $file->getClientOriginalName(),
                'file_path' => $path,
                'mime_type' => $file->getMimeType() ?? 'application/octet-stream',
                'file_size' => $file->getSize(),
            ]);
        }

        return redirect()->route('complaints.my')
            ->with('status', 'Pengaduan berhasil dikirim.');
    }

    public function myIndex(Request $request): View
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

    public function show(Complaint $complaint, Request $request): View
    {
        $user = $request->user();
        
        $isAllowed = $user->isAdmin() 
            || $complaint->reporter_id === $user->id
            || ($user->isInstansi() && $complaint->assigned_unit_id === $user->id);

        abort_if(!$isAllowed, 403);

        $complaint->load(['attachments', 'actions.actor']);

        return view('complaints.show', compact('complaint'));
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
