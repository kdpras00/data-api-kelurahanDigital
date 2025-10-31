<?php
// file untuk menampilkan apa saja yang ingin ditampilkan ke api atau hasil response, jadi agar lebih rapih. atau bisa juga ketika punya relasi menentukannya di resource.
namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Handle null resource
        if (is_null($this->resource)) {
            return [];
        }

        return [
            'id'             => $this->id,
            'name'           => $this->name,
            'email'          => $this->email,
            'profile_picture' => $this->profile_picture ? asset('storage/' . $this->profile_picture) : null,
            'identity_number' => $this->identity_number,
            'date_of_birth'  => $this->date_of_birth,
            'head_of_family' => new HeadOfFamilyResource($this->whenLoaded('headOfFamily')),
        ];
    }
}
