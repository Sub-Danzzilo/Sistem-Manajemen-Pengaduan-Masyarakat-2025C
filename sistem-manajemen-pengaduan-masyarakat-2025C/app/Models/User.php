<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Notification as CustomNotification;

#[Fillable(['name', 'email', 'password', 'role'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Override the default notifications relationship to use custom model.
     */
    public function notifications(): HasMany
    {
        return $this->hasMany(CustomNotification::class, 'notifiable_id')
            ->where('notifiable_type', $this->getMorphClass())
            ->latest();
    }

    public const ROLE_MASYARAKAT = 'masyarakat';
    public const ROLE_ADMIN = 'admin';
    public const ROLE_INSTANSI = 'instansi';

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function complaints(): HasMany
    {
        return $this->hasMany(Complaint::class, 'reporter_id');
    }

    public function assignedComplaints(): HasMany
    {
        return $this->hasMany(Complaint::class, 'assigned_unit_id');
    }

    public function verifiedComplaints(): HasMany
    {
        return $this->hasMany(Complaint::class, 'verified_by_id');
    }

    public function complaintActions(): HasMany
    {
        return $this->hasMany(ComplaintAction::class, 'actor_user_id');
    }

    public function isMasyarakat(): bool
    {
        return $this->role === self::ROLE_MASYARAKAT;
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isInstansi(): bool
    {
        return $this->role === self::ROLE_INSTANSI;
    }
}
