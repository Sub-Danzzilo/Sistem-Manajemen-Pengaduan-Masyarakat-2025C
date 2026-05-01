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
            $user = $notification->notifiable;
            if ($user) {
                // Keep only the latest 50 notifications
                $notificationsToKeep = 50;
                
                // Get IDs of notifications that exceed the limit (oldest ones)
                $idsToDelete = self::where('notifiable_id', $user->id)
                    ->where('notifiable_type', get_class($user))
                    ->latest()
                    ->skip($notificationsToKeep)
                    ->pluck('id');

                if ($idsToDelete->isNotEmpty()) {
                    self::whereIn('id', $idsToDelete)->delete();
                }
            }
        });
    }
}
