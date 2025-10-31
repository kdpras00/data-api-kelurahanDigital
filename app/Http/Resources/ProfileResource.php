<?php
namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfileResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'thumbnail'         => asset('storage/' . $this->thumbnail),
            'name'              => $this->name,
            'about'             => $this->about,
            'headman'           => $this->headman,
            'people'            => $this->people,
            'agricultural_area' => (float) (string) $this->agricultural_area,
            'total_area'        => (float) (string) $this->total_area,
            'latitude'          => $this->latitude ? (float) (string) $this->latitude : null,
            'longitude'         => $this->longitude ? (float) (string) $this->longitude : null,
            'profile_images'    => ProfileImageResource::collection($this->profileImages),
        ];
    }
}
