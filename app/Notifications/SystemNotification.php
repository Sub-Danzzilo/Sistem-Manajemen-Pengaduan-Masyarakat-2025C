<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SystemNotification extends Notification
{
    use Queueable;

    protected array $data;

    /**
     * Create a new notification instance.
     * @param array $data ['title' => string, 'message' => string, 'url' => string, 'type' => string]
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => $this->data['title'] ?? 'Notifikasi Sistem',
            'message' => $this->data['message'] ?? '',
            'url' => $this->data['url'] ?? '#',
            'type' => $this->data['type'] ?? 'info', // info, success, warning, danger
        ];
    }

}
