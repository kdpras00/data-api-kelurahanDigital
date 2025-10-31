<?php

namespace Database\Seeders;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Seeder;

class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get Lurah user
        $lurah = User::where('email', 'lurah@gmail.com')->first();
        
        // Get Head of Family user
        $headOfFamily = User::where('email', 'headoffamily@gmail.com')->first();

        if (!$lurah || !$headOfFamily) {
            $this->command->warn('Please run UserSeeder first!');
            return;
        }

        // Notifications for Lurah
        $lurahNotifications = [
            [
                'user_id' => $lurah->id,
                'type' => 'social-assistance',
                'title' => 'Pengajuan Bantuan Sosial Baru',
                'message' => 'Ada 3 pengajuan bantuan sosial baru yang menunggu persetujuan Anda.',
                'link' => '/social-assistance-recipient',
                'is_read' => false,
                'created_at' => now()->subHours(2),
            ],
            [
                'user_id' => $lurah->id,
                'type' => 'event',
                'title' => 'Event Gotong Royong Besok',
                'message' => 'Event Gotong Royong Kampung akan dilaksanakan besok pagi pukul 08.00 WIB.',
                'link' => '/event',
                'is_read' => false,
                'created_at' => now()->subHours(5),
            ],
            [
                'user_id' => $lurah->id,
                'type' => 'job-vacancy',
                'title' => 'Lamaran Pekerjaan Baru',
                'message' => 'Ada 2 lamaran pekerjaan baru untuk lowongan Security di PT. Maju Jaya.',
                'link' => '/job-vacancy',
                'is_read' => true,
                'read_at' => now()->subHours(1),
                'created_at' => now()->subDays(1),
            ],
            [
                'user_id' => $lurah->id,
                'type' => 'family',
                'title' => 'Data Kepala Keluarga Baru',
                'message' => '5 data kepala keluarga baru telah ditambahkan ke sistem.',
                'link' => '/head-of-family',
                'is_read' => true,
                'read_at' => now()->subDays(1),
                'created_at' => now()->subDays(2),
            ],
            [
                'user_id' => $lurah->id,
                'type' => 'system',
                'title' => 'Pembaruan Sistem',
                'message' => 'Sistem akan menjalani pemeliharaan pada Minggu, 3 November 2025 pukul 22.00 - 02.00 WIB.',
                'link' => null,
                'is_read' => false,
                'created_at' => now()->subDays(3),
            ],
        ];

        // Notifications for Head of Family
        $headOfFamilyNotifications = [
            [
                'user_id' => $headOfFamily->id,
                'type' => 'social-assistance',
                'title' => 'Pengajuan Bantuan Disetujui',
                'message' => 'Selamat! Pengajuan bantuan sosial PKH Anda telah disetujui. Silakan cek detail untuk informasi lebih lanjut.',
                'link' => '/social-assistance-recipient',
                'is_read' => false,
                'created_at' => now()->subHours(1),
            ],
            [
                'user_id' => $headOfFamily->id,
                'type' => 'event',
                'title' => 'Undangan Event Kampung',
                'message' => 'Anda diundang untuk mengikuti event Gotong Royong Kampung besok pukul 08.00 WIB.',
                'link' => '/event',
                'is_read' => false,
                'created_at' => now()->subHours(4),
            ],
            [
                'user_id' => $headOfFamily->id,
                'type' => 'job-vacancy',
                'title' => 'Lowongan Pekerjaan Baru',
                'message' => 'Ada lowongan pekerjaan baru yang mungkin cocok untuk Anda: Security di PT. Maju Jaya.',
                'link' => '/job-vacancy',
                'is_read' => true,
                'read_at' => now()->subHours(2),
                'created_at' => now()->subDays(1),
            ],
            [
                'user_id' => $headOfFamily->id,
                'type' => 'family',
                'title' => 'Data Anggota Keluarga Diperbarui',
                'message' => 'Data anggota keluarga Anda telah berhasil diperbarui.',
                'link' => '/family-member',
                'is_read' => true,
                'read_at' => now()->subDays(1),
                'created_at' => now()->subDays(2),
            ],
            [
                'user_id' => $headOfFamily->id,
                'type' => 'system',
                'title' => 'Selamat Datang di Sistem Kelurahan Digital',
                'message' => 'Terima kasih telah bergabung dengan Sistem Kelurahan Digital. Jelajahi fitur-fitur yang tersedia untuk kemudahan Anda.',
                'link' => null,
                'is_read' => true,
                'read_at' => now()->subDays(5),
                'created_at' => now()->subDays(7),
            ],
        ];

        // Insert all notifications
        foreach ($lurahNotifications as $notification) {
            Notification::create($notification);
        }

        foreach ($headOfFamilyNotifications as $notification) {
            Notification::create($notification);
        }

        $this->command->info('Notifications seeded successfully!');
    }
}
