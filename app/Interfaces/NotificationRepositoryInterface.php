<?php

namespace App\Interfaces;

interface NotificationRepositoryInterface
{
    public function getAllForUser(string $userId);
    public function getUnreadCount(string $userId);
    public function markAsRead(string $notificationId);
    public function markAllAsReadForUser(string $userId);
    public function delete(string $notificationId);
    public function create(array $data);
}
