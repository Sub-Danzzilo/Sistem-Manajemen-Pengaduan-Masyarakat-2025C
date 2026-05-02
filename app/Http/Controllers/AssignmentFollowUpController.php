<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use App\Models\ComplaintAction;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use App\Models\SystemLog;

class AssignmentFollowUpController extends Controller
{
    public function index(Request $request, string $account, string $role): View
    {
        $status = $request->string('status')->toString();

        $complaints = Complaint::query()
            ->with(['reporter', 'attachments'])
            ->where('assigned_unit_id', $request->user()->id)
            ->when($status !== '', fn ($query) => $query->where('status', $status))
            // Custom ordering: assigned (1), in_progress (2), resolved (3)
            ->orderByRaw("CASE 
                WHEN status = 'assigned' THEN 1 
                WHEN status = 'in_progress' THEN 2 
                WHEN status = 'resolved' THEN 3 
                ELSE 4 END")
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('instansi.complaints.index', compact('complaints', 'status'));
    }

    public function assign(Complaint $complaint, Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'assigned_unit_id' => ['required', 'integer', 'exists:users,id'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $unit = User::query()
            ->where('id', $validated['assigned_unit_id'])
            ->where('role', User::ROLE_INSTANSI)
            ->firstOrFail();

        $fromStatus = $complaint->status;

        DB::beginTransaction();

        try {
            $complaint->update([
                'assigned_unit_id' => $unit->id,
                'status' => Complaint::STATUS_ASSIGNED,
            ]);

            $this->logAction(
                complaint: $complaint,
                actorId: $request->user()->id,
                actionType: 'assigned',
                fromStatus: $fromStatus,
                toStatus: Complaint::STATUS_ASSIGNED,
                notes: $validated['notes'] ?? 'Pengaduan didisposisikan ke instansi.',
                meta: ['unit_name' => $unit->name, 'unit_id' => $unit->id],
            );

            DB::commit();
            return back()->with('status', 'Pengaduan berhasil didisposisikan.');
        } catch (\Throwable $e) {
            DB::rollBack();

            SystemLog::log(
                message: 'Gagal mendisposisikan pengaduan: ' . $e->getMessage(),
                category: 'AssignmentFollowUpController@assign',
                exception: $e
            );

            return back()->withErrors(['error' => 'Gagal mendisposisikan pengaduan.']);
        }
    }

    public function progress(string $account, string $role, Complaint $complaint, Request $request): RedirectResponse
    {
        $this->ensureOwnedByUnit($complaint, $request->user()->id);

        $validated = $request->validate([
            'notes' => ['required', 'string', 'max:2000'],
        ]);

        $fromStatus = $complaint->status;
        $isUpdate = $fromStatus === Complaint::STATUS_IN_PROGRESS;

        DB::beginTransaction();

        try {
            if (!$isUpdate) {
                $complaint->update(['status' => Complaint::STATUS_IN_PROGRESS]);
            }

            $this->logAction(
                complaint: $complaint,
                actorId: $request->user()->id,
                actionType: $isUpdate ? 'progress_updated' : 'in_progress',
                fromStatus: $fromStatus,
                toStatus: Complaint::STATUS_IN_PROGRESS,
                notes: $validated['notes'],
            );

            DB::commit();
            return back()->with('status', $isUpdate ? 'Catatan progres berhasil diperbarui.' : 'Status pengaduan diperbarui menjadi dalam proses.');
        } catch (\Throwable $e) {
            DB::rollBack();

            SystemLog::log(
                message: 'Gagal memperbarui progres: ' . $e->getMessage(),
                category: 'AssignmentFollowUpController@progress',
                exception: $e
            );

            return back()->withErrors(['error' => 'Gagal memperbarui progres pengaduan.']);
        }
    }

    public function resolve(string $account, string $role, Complaint $complaint, Request $request): RedirectResponse
    {
        $this->ensureOwnedByUnit($complaint, $request->user()->id);

        $validated = $request->validate([
            'notes' => ['required', 'string', 'max:2000'],
        ]);

        $fromStatus = $complaint->status;
        $isUpdate = $fromStatus === Complaint::STATUS_RESOLVED;

        DB::beginTransaction();

        try {
            if (!$isUpdate) {
                $complaint->update([
                    'status' => Complaint::STATUS_RESOLVED,
                    'resolved_at' => now(),
                ]);
            }

            $this->logAction(
                complaint: $complaint,
                actorId: $request->user()->id,
                actionType: $isUpdate ? 'resolve_updated' : 'resolved',
                fromStatus: $fromStatus,
                toStatus: Complaint::STATUS_RESOLVED,
                notes: $validated['notes'],
            );

            DB::commit();
            return back()->with('status', $isUpdate ? 'Hasil akhir berhasil diperbarui.' : 'Pengaduan ditandai selesai.');
        } catch (\Throwable $e) {
            DB::rollBack();

            SystemLog::log(
                message: 'Gagal menyelesaikan pengaduan: ' . $e->getMessage(),
                category: 'AssignmentFollowUpController@resolve',
                exception: $e
            );

            return back()->withErrors(['error' => 'Gagal menandai pengaduan selesai.']);
        }
    }

    public static function availableUnits()
    {
        return User::query()
            ->where('role', User::ROLE_INSTANSI)
            ->orderBy('name')
            ->get();
    }

    private function ensureOwnedByUnit(Complaint $complaint, int $userId): void
    {
        abort_if($complaint->assigned_unit_id !== $userId, 403);
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
