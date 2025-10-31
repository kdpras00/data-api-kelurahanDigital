<?php
namespace App\Repositories;

use App\Interfaces\SearchRepositoryInterface;
use App\Models\Event;
use App\Models\HeadOfFamily;
use App\Models\JobVacancy;
use App\Models\SocialAssistance;
use App\Models\SocialAssistanceRecipient;

class SearchRepository implements SearchRepositoryInterface
{
    public function globalSearch(string $query)
    {
        return [
            'headOfFamilies' => HeadOfFamily::with('user')
                ->where(function ($q) use ($query) {
                    $q->whereHas('user', function ($userQuery) use ($query) {
                        $userQuery->where('name', 'like', "%{$query}%");
                    })
                    ->orWhereHas('user', function ($userQuery) use ($query) {
                        $userQuery->where('identity_number', 'like', "%{$query}%");
                    })
                    ->orWhere('identity_number', 'like', "%{$query}%")
                    ->orWhere('occupation', 'like', "%{$query}%");
                })
                ->withCount('familyMembers')
                ->get()
                ->map(function ($headOfFamily) {
                    return [
                        'id' => $headOfFamily->id,
                        'name' => $headOfFamily->user?->name ?? 'Unknown',
                        'identity_number' => $headOfFamily->identity_number,
                        'occupation' => $headOfFamily->occupation,
                        'profile_picture' => $headOfFamily->profile_picture ? asset('storage/' . $headOfFamily->profile_picture) : null,
                        'family_members_count' => $headOfFamily->family_members_count,
                    ];
                }),

            'socialAssistances' => SocialAssistance::where(function ($q) use ($query) {
                    $q->where('name', 'like', "%{$query}%")
                      ->orWhere('provider', 'like', "%{$query}%")
                      ->orWhere('category', 'like', "%{$query}%");
                })
                ->withCount('socialAssistanceRecipients as recipients_count')
                ->get()
                ->map(function ($assistance) {
                    return [
                        'id' => $assistance->id,
                        'name' => $assistance->name,
                        'category' => $assistance->category,
                        'amount' => $assistance->amount,
                        'provider' => $assistance->provider,
                        'is_available' => $assistance->is_available,
                        'thumbnail' => $assistance->thumbnail ? asset('storage/' . $assistance->thumbnail) : null,
                        'recipients_count' => $assistance->recipients_count,
                    ];
                }),

            'socialAssistanceRecipients' => SocialAssistanceRecipient::with(['socialAssistance', 'headOfFamily.user'])
                ->where(function($q) use ($query) {
                    $q->whereHas('headOfFamily.user', function ($subQ) use ($query) {
                        $subQ->where('name', 'like', "%{$query}%");
                    })
                    ->orWhereHas('socialAssistance', function ($subQ) use ($query) {
                        $subQ->where('name', 'like', "%{$query}%");
                    });
                })
                ->get()
                ->map(function ($recipient) {
                    return [
                        'id' => $recipient->id,
                        'amount' => $recipient->amount,
                        'status' => $recipient->status,
                        'created_at' => $recipient->created_at,
                        'social_assistance' => [
                            'name' => $recipient->socialAssistance?->name ?? null,
                            'category' => $recipient->socialAssistance?->category ?? null,
                            'provider' => $recipient->socialAssistance?->provider ?? null,
                            'thumbnail' => $recipient->socialAssistance?->thumbnail ? asset('storage/' . $recipient->socialAssistance->thumbnail) : null,
                        ],
                        'head_of_family' => [
                            'name' => $recipient->headOfFamily?->user?->name ?? 'Unknown',
                            'occupation' => $recipient->headOfFamily?->occupation ?? null,
                            'profile_picture' => $recipient->headOfFamily?->profile_picture ? asset('storage/' . $recipient->headOfFamily->profile_picture) : null,
                        ],
                    ];
                }),

            'jobVacancies' => JobVacancy::where(function ($q) use ($query) {
                    $q->where('job_title', 'like', "%{$query}%")
                      ->orWhere('company_in_charge', 'like', "%{$query}%");
                })
                ->withCount('jobApplicants as applicants_count')
                ->get()
                ->map(function ($job) {
                    return [
                        'id' => $job->id,
                        'position' => $job->job_title,
                        'company' => $job->company_in_charge,
                        'salary' => $job->salary,
                        'application_deadline' => $job->end_date,
                        'thumbnail' => $job->thumbnail ? asset('storage/' . $job->thumbnail) : null,
                        'applicants_count' => $job->applicants_count,
                    ];
                }),

            'events' => Event::where('name', 'like', "%{$query}%")
                ->withCount('eventParticipants as participants_count')
                ->get()
                ->map(function ($event) {
                    return [
                        'id' => $event->id,
                        'name' => $event->name,
                        'start_date' => $event->date,
                        'start_time' => $event->time,
                        'price' => $event->price,
                        'is_active' => $event->is_active,
                        'thumbnail' => $event->thumbnail ? asset('storage/' . $event->thumbnail) : null,
                        'participants_count' => $event->participants_count,
                    ];
                }),
        ];
    }
}



