<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Http\Resources\NotificationResource;
use App\Interfaces\NotificationRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    protected $notificationRepository;

    public function __construct(NotificationRepositoryInterface $notificationRepository)
    {
        $this->notificationRepository = $notificationRepository;
    }

    /**
     * Get all notifications for authenticated user
     */
    public function index()
    {
        try {
            $userId = Auth::id();
            $notifications = $this->notificationRepository->getAllForUser($userId);

            return ResponseHelper::jsonResponse(
                true,
                'Notifikasi berhasil diambil',
                NotificationResource::collection($notifications),
                200
            );
        } catch (\Exception $e) {
            return ResponseHelper::jsonResponse(
                false,
                'Gagal mengambil notifikasi',
                null,
                500
            );
        }
    }

    /**
     * Get unread notifications count
     */
    public function unreadCount()
    {
        try {
            $userId = Auth::id();
            $count = $this->notificationRepository->getUnreadCount($userId);

            return ResponseHelper::jsonResponse(
                true,
                'Jumlah notifikasi belum dibaca berhasil diambil',
                ['count' => $count],
                200
            );
        } catch (\Exception $e) {
            return ResponseHelper::jsonResponse(
                false,
                'Gagal mengambil jumlah notifikasi',
                null,
                500
            );
        }
    }

    /**
     * Mark a notification as read
     */
    public function markAsRead($id)
    {
        try {
            $notification = $this->notificationRepository->markAsRead($id);

            return ResponseHelper::jsonResponse(
                true,
                'Notifikasi berhasil ditandai sebagai dibaca',
                new NotificationResource($notification),
                200
            );
        } catch (\Exception $e) {
            return ResponseHelper::jsonResponse(
                false,
                'Gagal menandai notifikasi sebagai dibaca',
                null,
                500
            );
        }
    }

    /**
     * Mark all notifications as read for authenticated user
     */
    public function markAllAsRead()
    {
        try {
            $userId = Auth::id();
            $this->notificationRepository->markAllAsReadForUser($userId);

            return ResponseHelper::jsonResponse(
                true,
                'Semua notifikasi berhasil ditandai sebagai dibaca',
                null,
                200
            );
        } catch (\Exception $e) {
            return ResponseHelper::jsonResponse(
                false,
                'Gagal menandai semua notifikasi sebagai dibaca',
                null,
                500
            );
        }
    }

    /**
     * Delete a notification
     */
    public function destroy($id)
    {
        try {
            $this->notificationRepository->delete($id);

            return ResponseHelper::jsonResponse(
                true,
                'Notifikasi berhasil dihapus',
                null,
                200
            );
        } catch (\Exception $e) {
            return ResponseHelper::jsonResponse(
                false,
                'Gagal menghapus notifikasi',
                null,
                500
            );
        }
    }
}
