<?php
namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class JobApplicantResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Map 'approved' to 'accepted' for frontend consistency
        $status = $this->status === 'approved' ? 'accepted' : $this->status;
        
        return [
            'id'          => $this->id,
            'job_vacancy' => new JobVacancyResource($this->jobVacancy),
            'user'        => new UserResource($this->user),
            'status'      => $status,
            'created_at'  => $this->created_at,
        ];
    }
}