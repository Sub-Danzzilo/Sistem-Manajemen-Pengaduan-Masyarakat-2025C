<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use App\Models\ComplaintAction;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VerificationController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->string('status')->toString();

        $complaints = Complaint::query()
            ->with(['reporter', 'assignedUnit'])
            ->when($status !== '', fn ($query) => $query->where('status', $status))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $units = User::query()
            ->where('role', User::ROLE_INSTANSI)
            ->orderBy('name')
            ->get();

        return view('admin.complaints.index', compact('complaints', 'status', 'units'));
    }

    public function decision(Complaint $complaint, Request $request): RedirectResponse
    {
        $isAlreadyProcessed = $complaint->status !== Complaint::STATUS_SUBMITTED;

        $validated = $request->validate([
            'decision' => [$isAlreadyProcessed ? 'nullable' : 'required', 'in:accepted,rejected'],
            'category' => ['nullable', 'string', 'max:100'],
            'assigned_unit_id' => ['nullable', 'integer', 'exists:users,id'],
            'message_for_citizen' => ['required_if:decision,accepted', 'nullable', 'string', 'max:2000'],
            'instruction_for_unit' => ['nullable', 'string', 'max:2000'],
            'rejection_reason' => ['nullable', 'string', 'max:2000'],
        ]);

        $fromStatus = $complaint->status;
        $adminUser = $request->user();
        
        // Use current decision if already processed
        $decision = $isAlreadyProcessed 
            ? ($complaint->status === Complaint::STATUS_REJECTED ? 'rejected' : 'accepted')
            : $validated['decision'];

        if ($decision === 'accepted') {
            $request->validate([
                'assigned_unit_id' => ['required', 'integer', 'exists:users,id'],
                'instruction_for_unit' => ['required', 'string', 'max:2000'],
            ]);

            $unit = User::query()
                ->where('id', (int) $validated['assigned_unit_id'])
                ->where('role', User::ROLE_INSTANSI)
                ->firstOrFail();

            if (!$isAlreadyProcessed) {
                $complaint->update([
                    'status' => Complaint::STATUS_ASSIGNED,
                    'verified_by_id' => $adminUser->id,
                ]);
            }

            $complaint->update([
                'assigned_unit_id' => $unit->id,
                'category' => $validated['category'] ?? $complaint->category,
            ]);

            $this->logAction(
                complaint: $complaint,
                actorId: $adminUser->id,
                actionType: $isAlreadyProcessed ? 'decision_updated' : 'decision_accepted',
                fromStatus: $fromStatus,
                toStatus: $isAlreadyProcessed ? $complaint->status : Complaint::STATUS_ASSIGNED,
                notes: $validated['instruction_for_unit'],
                meta: [
                    'decision' => 'accepted',
                    'target_unit_id' => $unit->id,
                    'target_unit_name' => $unit->name,
                    'message_for_citizen' => $validated['message_for_citizen'] ?? ($complaint->actions()->where('action_type', 'decision_accepted')->first()?->meta['message_for_citizen'] ?? ''),
                    'instruction_for_unit' => $validated['instruction_for_unit'],
                ],
            );

            return back()->with('status', 'Data verifikasi berhasil diperbarui.');
        }

        // Rejected Case
        $rejectionData = $request->validate([
            'rejection_reason' => ['required', 'string', 'max:2000'],
        ]);

        if (!$isAlreadyProcessed) {
            $complaint->update([
                'status' => Complaint::STATUS_REJECTED,
                'verified_by_id' => $adminUser->id,
            ]);
        }

        $this->logAction(
            complaint: $complaint,
            actorId: $adminUser->id,
            actionType: $isAlreadyProcessed ? 'decision_updated' : 'decision_rejected',
            fromStatus: $fromStatus,
            toStatus: $isAlreadyProcessed ? $complaint->status : Complaint::STATUS_REJECTED,
            notes: $rejectionData['rejection_reason'],
            meta: [
                'decision' => 'rejected',
                'rejection_reason' => $rejectionData['rejection_reason'],
                'message_for_citizen' => $rejectionData['rejection_reason'], // Use reason as message
            ],
        );

        return back()->with('status', 'Data penolakan berhasil diperbarui.');
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
