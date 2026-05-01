<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Complaint extends Model
{
    public const STATUS_SUBMITTED = 'submitted';
    public const STATUS_VERIFIED = 'verified';
    public const STATUS_ASSIGNED = 'assigned';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_RESOLVED = 'resolved';
    public const STATUS_REJECTED = 'rejected';

    protected $fillable = [
        'reporter_id',
        'assigned_unit_id',
        'verified_by_id',
        'title',
        'description',
        'location_text',
        'category',
        'status',
        'resolved_at',
    ];

    protected function casts(): array
    {
        return [
            'resolved_at' => 'datetime',
        ];
    }

    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    public function assignedUnit(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_unit_id');
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by_id');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(ComplaintAttachment::class);
    }

    public function actions(): HasMany
    {
        return $this->hasMany(ComplaintAction::class)->latest();
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_SUBMITTED => 'Menunggu Verifikasi',
            self::STATUS_VERIFIED => 'Diverifikasi',
            self::STATUS_ASSIGNED => 'Ditugaskan',
            self::STATUS_IN_PROGRESS => 'Diproses',
            self::STATUS_RESOLVED => 'Selesai',
            self::STATUS_REJECTED => 'Ditolak',
            default => 'Tidak Diketahui',
        };
    }
}
