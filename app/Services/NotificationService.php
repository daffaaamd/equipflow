<?php

namespace App\Services;

use App\Models\AppNotification;

class NotificationService
{
    public static function send(int $userId, string $title, string $message, string $type = 'info', ?string $link = null): void
    {
        AppNotification::create([
            'user_id' => $userId,
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'link' => $link,
        ]);
    }

    public static function unreadCount(int $userId): int
    {
        return AppNotification::where('user_id', $userId)->whereNull('read_at')->count();
    }
}