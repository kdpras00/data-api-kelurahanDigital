<?php
namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SocialAssistanceRecipientResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        // Handle proof URL - check if it's already a full URL or a path
        $proofUrl = null;
        if ($this->proof) {
            // If proof starts with http/https, it's already a full URL (e.g., placeholder from seeder)
            if (str_starts_with($this->proof, 'http://') || str_starts_with($this->proof, 'https://')) {
                $proofUrl = $this->proof;
            } else {
                // Otherwise, it's a path that needs storage prefix
                $proofUrl = asset('storage/' . $this->proof);
            }
        }

        return [
            'id'                => $this->id,
            'social_assistance' => new SocialAssistanceResource($this->socialAssistance),

            'head_of_family'    => $this->headOfFamily ? new HeadOfFamilyResource($this->headOfFamily) : null,
            'amount'            => $this->amount,
            'reason'            => $this->reason,
            'bank'              => $this->bank,
            'account_number'    => $this->account_number,
            'proof'             => $proofUrl,
            'status'            => $this->status,
            'created_at'        => $this->created_at,
        ];
    }
}
