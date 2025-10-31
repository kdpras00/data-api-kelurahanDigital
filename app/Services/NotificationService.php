<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;

class NotificationService
{
    /**
     * Create notification for specific user
     */
    public function createForUser($userId, $type, $title, $message, $link = null)
    {
        return Notification::create([
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'link' => $link,
            'is_read' => false,
        ]);
    }

    /**
     * Create notification for multiple users
     */
    public function createForUsers($userIds, $type, $title, $message, $link = null)
    {
        $notifications = [];
        foreach ($userIds as $userId) {
            $notifications[] = [
                'user_id' => $userId,
                'type' => $type,
                'title' => $title,
                'message' => $message,
                'link' => $link,
                'is_read' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        
        Notification::insert($notifications);
        return count($notifications);
    }

    /**
     * Create notification for all users with specific role
     */
    public function createForRole($role, $type, $title, $message, $link = null)
    {
        $users = User::role($role)->pluck('id')->toArray();
        return $this->createForUsers($users, $type, $title, $message, $link);
    }

    /**
     * Create notification for all lurah users
     */
    public function notifyLurah($type, $title, $message, $link = null)
    {
        return $this->createForRole('lurah', $type, $title, $message, $link);
    }

    /**
     * Create notification for all head-of-family users
     */
    public function notifyHeadOfFamily($type, $title, $message, $link = null)
    {
        return $this->createForRole('head-of-family', $type, $title, $message, $link);
    }

    /**
     * Create notification for all users
     */
    public function notifyAll($type, $title, $message, $link = null)
    {
        $users = User::pluck('id')->toArray();
        return $this->createForUsers($users, $type, $title, $message, $link);
    }

    /**
     * Job Application Notifications
     */
    public function jobApplicationCreated($jobVacancy, $applicant)
    {
        // Load user relation if not already loaded
        if (!$applicant->relationLoaded('user')) {
            $applicant->load('user');
        }
        
        $applicantName = $applicant->user->name ?? 'Pelamar';
        
        // Notify Lurah: New job application
        $this->notifyLurah(
            'job-vacancy',
            'Lamaran Pekerjaan Baru',
            "Ada lamaran baru untuk posisi {$jobVacancy->position} dari {$applicantName}.",
            '/job-vacancy'
        );

        // Notify Applicant: Application submitted
        if ($applicant->user_id) {
            $this->createForUser(
                $applicant->user_id,
                'job-vacancy',
                'Lamaran Terkirim',
                "Lamaran Anda untuk posisi {$jobVacancy->position} telah berhasil dikirim.",
                '/job-vacancy'
            );
        }
    }

    public function jobApplicationStatusChanged($jobVacancy, $applicant, $status)
    {
        $statusText = $status === 'accepted' ? 'diterima' : 'ditolak';
        
        // Notify Applicant: Application status changed
        if ($applicant->user_id) {
            $this->createForUser(
                $applicant->user_id,
                'job-vacancy',
                'Status Lamaran Diperbarui',
                "Lamaran Anda untuk posisi {$jobVacancy->position} telah {$statusText}.",
                '/job-vacancy'
            );
        }
    }

    /**
     * Social Assistance Notifications
     */
    public function socialAssistanceRecipientCreated($socialAssistance, $headOfFamily)
    {
        // Load user relation if not already loaded
        if (!$headOfFamily->relationLoaded('user')) {
            $headOfFamily->load('user');
        }
        
        $headOfFamilyName = $headOfFamily->user->name ?? 'Kepala Keluarga';
        
        // Notify Lurah: New social assistance application
        $this->notifyLurah(
            'social-assistance',
            'Pengajuan Bantuan Sosial Baru',
            "Ada pengajuan baru untuk bantuan {$socialAssistance->name} dari {$headOfFamilyName}.",
            '/social-assistance-recipient'
        );

        // Notify Head of Family: Application submitted
        if ($headOfFamily->user_id) {
            $this->createForUser(
                $headOfFamily->user_id,
                'social-assistance',
                'Pengajuan Terkirim',
                "Pengajuan bantuan {$socialAssistance->name} Anda telah berhasil dikirim.",
                '/social-assistance-recipient'
            );
        }
    }

    public function socialAssistanceRecipientStatusChanged($socialAssistance, $headOfFamily, $status)
    {
        $statusText = $status === 'approved' ? 'disetujui' : ($status === 'rejected' ? 'ditolak' : 'diperbarui');
        
        // Notify Head of Family: Status changed
        if ($headOfFamily->user_id) {
            $this->createForUser(
                $headOfFamily->user_id,
                'social-assistance',
                'Status Pengajuan Bantuan',
                "Pengajuan bantuan {$socialAssistance->name} Anda telah {$statusText}.",
                '/social-assistance-recipient'
            );
        }
    }

    /**
     * Event Notifications
     */
    public function eventCreated($event)
    {
        // Notify all users: New event
        $this->notifyAll(
            'event',
            'Event Baru: ' . $event->name,
            "Ada event baru di desa! {$event->name} akan dilaksanakan pada " . date('d M Y', strtotime($event->start_date)) . ".",
            '/event'
        );
    }

    public function eventParticipantRegistered($event, $participant)
    {
        // Load user relation if not already loaded
        if (!$participant->relationLoaded('user')) {
            $participant->load('user');
        }
        
        $participantName = $participant->user->name ?? 'Peserta';
        
        // Notify Lurah: New participant
        $this->notifyLurah(
            'event',
            'Peserta Event Baru',
            "{$participantName} telah mendaftar untuk event {$event->name}.",
            '/event'
        );

        // Notify Participant: Registration confirmed
        if ($participant->user_id) {
            $this->createForUser(
                $participant->user_id,
                'event',
                'Pendaftaran Event Berhasil',
                "Pendaftaran Anda untuk event {$event->name} telah berhasil.",
                '/event'
            );
        }
    }

    /**
     * Social Assistance Notifications (New/Updated)
     */
    public function socialAssistanceCreated($socialAssistance)
    {
        // Notify all head-of-family: New social assistance available
        $this->notifyHeadOfFamily(
            'social-assistance',
            'Bantuan Sosial Baru Tersedia',
            "Bantuan sosial baru '{$socialAssistance->name}' telah tersedia. Segera ajukan jika memenuhi syarat!",
            '/social-assistance'
        );
    }

    /**
     * Family Member Notifications
     */
    public function familyMemberAdded($familyMember, $headOfFamily)
    {
        // Load user relation if not already loaded
        if (!$headOfFamily->relationLoaded('user')) {
            $headOfFamily->load('user');
        }
        
        $headOfFamilyName = $headOfFamily->user->name ?? 'Kepala Keluarga';
        
        // Notify Lurah: New family member
        $this->notifyLurah(
            'family',
            'Data Anggota Keluarga Baru',
            "Anggota keluarga baru ({$familyMember->name}) telah ditambahkan oleh {$headOfFamilyName}.",
            '/head-of-family'
        );

        // Notify Head of Family: Family member added
        if ($headOfFamily->user_id) {
            $this->createForUser(
                $headOfFamily->user_id,
                'family',
                'Anggota Keluarga Ditambahkan',
                "Data anggota keluarga {$familyMember->name} telah berhasil ditambahkan.",
                '/family-member'
            );
        }
    }

    /**
     * Head of Family Notifications
     */
    public function headOfFamilyCreated($headOfFamily)
    {
        // Load user relation if not already loaded
        if (!$headOfFamily->relationLoaded('user')) {
            $headOfFamily->load('user');
        }
        
        $headOfFamilyName = $headOfFamily->user->name ?? 'Kepala Keluarga';
        
        // Notify Lurah: New head of family
        $this->notifyLurah(
            'family',
            'Kepala Keluarga Baru',
            "Kepala keluarga baru ({$headOfFamilyName}) telah terdaftar di sistem.",
            '/head-of-family'
        );

        // Notify Head of Family: Welcome
        if ($headOfFamily->user_id) {
            $this->createForUser(
                $headOfFamily->user_id,
                'system',
                'Selamat Datang di Sistem Kelurahan Digital',
                "Akun Anda telah berhasil terdaftar. Nikmati berbagai layanan yang tersedia.",
                '/profile'
            );
        }
    }
}

