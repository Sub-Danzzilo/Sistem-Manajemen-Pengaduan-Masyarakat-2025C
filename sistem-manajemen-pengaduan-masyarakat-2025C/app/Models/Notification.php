<?php

namespace App\Models;

use Illuminate\Notifications\DatabaseNotification;

class Notification extends DatabaseNotification
{
    protected $table = 'notifications';

    /**
     * Boot the model and add a listener to enforce the 50-limit.
     */
    protected static function booted()
    {
        static::created(function ($notification) {
            try {
                if ($notification->notifiable_id) {
                    $notifiableId = $notification->notifiable_id;
                    $notifiableType = $notification->notifiable_type;

                    // Keep only the latest 50 notifications
                    // SQLite compatible approach: get IDs to keep, then delete others
                    $idsToKeep = self::where('notifiable_id', $notifiableId)
                        ->where('notifiable_type', $notifiableType)
                        ->latest()
                        ->limit(50)
                        ->pluck('id');

                    if ($idsToKeep->isNotEmpty()) {
                        self::where('notifiable_id', $notifiableId)
                            ->where('notifiable_type', $notifiableType)
                            ->whereNotIn('id', $idsToKeep)
                            ->delete();
                    }
                }
            } catch (\Throwable $e) {
                // Log error but don't crash the main process
                \Illuminate\Support\Facades\Log::error('Gagal membersihkan notifikasi lama: ' . $e->getMessage());
            }
        });
    }

}
