<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
