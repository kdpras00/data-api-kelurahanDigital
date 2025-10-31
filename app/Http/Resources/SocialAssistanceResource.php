<?php
namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SocialAssistanceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Handle thumbnail URL - check if it's already a full URL or a path
        $thumbnailUrl = null;
        if ($this->thumbnail) {
            // If thumbnail starts with http/https, it's already a full URL (e.g., placeholder from seeder)
            if (str_starts_with($this->thumbnail, 'http://') || str_starts_with($this->thumbnail, 'https://')) {
                $thumbnailUrl = $this->thumbnail;
            } else {
                // Otherwise, it's a path that needs storage prefix
                $thumbnailUrl = asset('storage/' . $this->thumbnail);
            }
        }

        return [
            'id'                           => $this->id,
            'thumbnail'                    => $thumbnailUrl,
            'name'                         => $this->name,
            'category'                     => $this->category,
            'amount'                       => $this->amount,
            'provider'                     => $this->provider,
            'description'                  => $this->description,
            'is_available'                 => $this->is_available,
            'social_assistance_recipients' => SocialAssistanceRecipientResource::collection($this->whenLoaded('socialAssistanceRecipients')),
        ];
    }
}
