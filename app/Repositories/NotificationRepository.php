<?php

namespace App\Repositories;

use App\Interfaces\NotificationRepositoryInterface;
use App\Models\Notification;

class NotificationRepository implements NotificationRepositoryInterface
{
    protected $notification;

    public function __construct(Notification $notification)
    {
        $this->notification = $notification;
    }

    public function getAllForUser(string $userId)
    {
        return $this->notification
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getUnreadCount(string $userId)
    {
        return $this->notification
            ->where('user_id', $userId)
            ->where('is_read', false)
            ->count();
    }

    public function markAsRead(string $notificationId)
    {
        $notification = $this->notification->findOrFail($notificationId);
        $notification->markAsRead();
        return $notification;
    }

    public function markAllAsReadForUser(string $userId)
    {
        return $this->notification
            ->where('user_id', $userId)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
    }

    public function delete(string $notificationId)
    {
        $notification = $this->notification->findOrFail($notificationId);
        return $notification->delete();
    }

    public function create(array $data)
    {
        return $this->notification->create($data);
    }
}

