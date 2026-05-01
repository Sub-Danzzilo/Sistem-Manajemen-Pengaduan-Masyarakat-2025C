<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use App\Notifications\SystemNotification;
use Illuminate\Support\Facades\Notification;

class ComplaintAction extends Model
{

    protected $fillable = [
        'complaint_id',
        'actor_user_id',
        'action_type',
        'from_status',
        'to_status',
        'notes',
        'meta',
    ];

    protected static function booted()
    {
        static::created(function ($action) {
            $complaint = $action->complaint;
            if (!$complaint) return;

            $notifiedUsers = collect();
            $notificationData = [
                'title' => $action->action_label,
                'message' => $action->notes ?: "Ada pembaruan pada laporan \"{$complaint->title}\"",
                'url' => route('complaints.show', [
                    'account' => 'notifikasi', // Dummy values as placeholders, middleware will handle
                    'role' => 'redirect',
                    'complaint' => $complaint->id
                ]),
                'complaint_id' => $complaint->id,
                'type' => 'info'
            ];


            // 1. Notify Admins for new submissions
            if ($action->action_type === 'submitted') {
                $admins = User::where('role', User::ROLE_ADMIN)->get();
                $notificationData['message'] = "Laporan baru: \"{$complaint->title}\" menunggu verifikasi.";
                $notificationData['type'] = 'warning';
                Notification::send($admins, new SystemNotification($notificationData));
            }

            // 2. Notify Citizen for decisions and progress
            $isCitizenAction = in_array($action->action_type, [
                'decision_accepted', 'decision_rejected', 'decision_updated',
                'in_progress', 'resolved', 'progress_updated', 'resolve_updated'
            ]);

            if ($isCitizenAction && $complaint->reporter) {
                $citizenData = $notificationData;
                if ($action->action_type === 'decision_accepted') {
                    $citizenData['message'] = "Laporan Anda telah diterima dan akan segera ditindaklanjuti.";
                    $citizenData['type'] = 'success';
                } elseif ($action->action_type === 'decision_rejected') {
                    $citizenData['message'] = "Laporan Anda ditolak: " . ($action->notes ?: 'Tidak ada alasan spesifik.');
                    $citizenData['type'] = 'danger';
                } elseif ($action->action_type === 'resolved') {
                    $citizenData['message'] = "Laporan Anda telah selesai ditangani.";
                    $citizenData['type'] = 'success';
                }
                
                // For citizen, the URL needs to be correctly formatted by the helper or we use a redirect route
                $complaint->reporter->notify(new SystemNotification($citizenData));
            }

            // 3. Notify Instansi for assignments
            if ($action->action_type === 'decision_accepted' || $action->action_type === 'decision_updated') {
                $unitId = data_get($action->meta, 'target_unit_id');
                if ($unitId) {
                    $unit = User::find($unitId);
                    if ($unit) {
                        $unitData = $notificationData;
                        $unitData['title'] = 'Penugasan Baru';
                        $unitData['message'] = "Anda mendapat penugasan baru: \"{$complaint->title}\"";
                        $unitData['type'] = 'info';
                        $unit->notify(new SystemNotification($unitData));
                    }
                }
            }
        });
    }

    protected function casts(): array
    {
        return [
            'meta' => 'array',
        ];
    }

    public function complaint(): BelongsTo
    {
        return $this->belongsTo(Complaint::class);
    }

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_user_id');
    }

    public function getActionLabelAttribute(): string
    {
        return match ($this->action_type) {
            'submitted' => 'Pengaduan Masuk',
            'verified' => 'Diverifikasi',
            'decision_accepted' => 'Diterima oleh Admin',
            'decision_rejected' => 'Ditolak oleh Admin',
            'decision_updated' => 'Pembaruan Keputusan',
            'assigned' => 'Ditugaskan ke Instansi',
            'in_progress' => 'Diproses oleh Instansi',
            'progress_updated' => 'Pembaruan Proses',
            'resolved' => 'Diselesaikan',
            'resolve_updated' => 'Pembaruan Penyelesaian',
            default => ucfirst(str_replace('_', ' ', $this->action_type)),
        };
    }
}
