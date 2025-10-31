<?php
namespace App\Repositories;

use App\Interfaces\DashboardRepositoryInterface;
use App\Models\Event;
use App\Models\FamilyMember;
use App\Models\HeadOfFamily;
use App\Models\JobApplicant;
use App\Models\JobVacancy;
use App\Models\SocialAssistance;
use App\Models\SocialAssistanceRecipient;

class DashboardRepository implements DashboardRepositoryInterface
{
    public function getDashboardData()
    {
        $data = [
            // Statistics counts - always return these
            'residents'          => HeadOfFamily::count() + FamilyMember::count(),
            'head_of_families'   => HeadOfFamily::count(),
            'social_assistances' => SocialAssistance::count(),
            'events'             => Event::count(),
            'job_applicants'     => JobApplicant::count(),
            'job_vacancies'      => JobVacancy::count(),
        ];

        // Try to get recent data, but don't fail if there's an error
        try {
            $data['recent_social_assistance_recipients'] = SocialAssistanceRecipient::with(['socialAssistance', 'headOfFamily.user'])
                ->latest()
                ->take(4)
                ->get()
                ->map(function ($recipient) {
                    return [
                        'id' => $recipient->id,
                        'amount' => $recipient->amount,
                        'status' => $recipient->status,
                        'created_at' => $recipient->created_at,
                        'social_assistance' => [
                            'name' => $recipient->socialAssistance->name ?? null,
                            'category' => $recipient->socialAssistance->category ?? null,
                        ],
                        'applicant' => [
                            'name' => $recipient->headOfFamily->user->name ?? 'Unknown',
                        ],
                    ];
                })
                ->values()
                ->toArray();
        } catch (\Exception $e) {
            $data['recent_social_assistance_recipients'] = [];
        }

        try {
            $data['recent_job_applicants'] = JobApplicant::with(['jobVacancy', 'user'])
                ->latest()
                ->take(3)
                ->get()
                ->map(function ($applicant) {
                    return [
                        'id' => $applicant->id,
                        'status' => $applicant->status,
                        'created_at' => $applicant->created_at,
                        'job_vacancy' => [
                            'position' => $applicant->jobVacancy->position ?? null,
                            'company' => $applicant->jobVacancy->company ?? null,
                            'thumbnail' => $applicant->jobVacancy->thumbnail ? asset('storage/' . $applicant->jobVacancy->thumbnail) : null,
                        ],
                        'applicant' => [
                            'name' => $applicant->user->name ?? 'Unknown',
                            'profile_picture' => $applicant->user->profile_picture ? asset('storage/' . $applicant->user->profile_picture) : null,
                        ],
                    ];
                })
                ->values()
                ->toArray();
        } catch (\Exception $e) {
            $data['recent_job_applicants'] = [];
        }

        try {
            $data['upcoming_events'] = Event::where('start_date', '>=', now())
                ->orderBy('start_date', 'asc')
                ->take(2)
                ->get()
                ->map(function ($event) {
                    return [
                        'id' => $event->id,
                        'name' => $event->name,
                        'start_date' => $event->start_date,
                        'start_time' => $event->start_time,
                        'thumbnail' => $event->thumbnail ? asset('storage/' . $event->thumbnail) : null,
                    ];
                })
                ->values()
                ->toArray();
        } catch (\Exception $e) {
            $data['upcoming_events'] = [];
        }

        return $data;
    }
}
