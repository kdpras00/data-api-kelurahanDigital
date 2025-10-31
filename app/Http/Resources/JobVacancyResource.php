<?php
namespace App\Http\Resources;

use App\Http\Resources\JobApplicantResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class JobVacancyResource extends JsonResource
{
    public function toArray(Request $request): array
    {

        return [
            'id'                => $this->id,
            'thumbnail'         => asset('storage/' . $this->thumbnail),
            'job_title'         => $this->job_title,
            'position'          => $this->job_title, // Alias for consistency with frontend
            'description'       => $this->description,
            'company_in_charge' => $this->company_in_charge,
            'company'           => $this->company_in_charge, // Alias for consistency with frontend
            'start_date'        => $this->start_date,
            'end_date'          => $this->end_date,
            'salary'            => (float) (string) $this->salary,
            'job_status'        => $this->job_status,
            'job_applicants'    => JobApplicantResource::collection($this->whenLoaded('jobApplicants')),
        ];
    }
}
