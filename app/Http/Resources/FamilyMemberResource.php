<?php
namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FamilyMemberResource extends JsonResource
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
            'id'              => $this->id,
            /* jika ingin memanggil relasi:
            1. panggil relasi dengan nama tabel yang direlasikan
            2. jika relasi pada modelnya belongs to atau memanggil, bisa menggunakan method new dengan memanggil resource yang tabelnya terkait dengan relasinya.
            3. jika has many menggunakan collection.
            */
            'head_of_family'  => $this->whenLoaded('headOfFamily') ? new HeadOfFamilyResource($this->whenLoaded('headOfFamily')) : null,
            'user'            => $this->user ? new UserResource($this->user) : null,
            'user_id'         => $this->user_id,
            'profile_picture' => $this->profile_picture ? asset('storage/' . $this->profile_picture) : null,
            'identity_number' => $this->identity_number,
            'gender'          => $this->gender,
            'date_of_birth'   => $this->date_of_birth,
            'phone_number'    => $this->phone_number,
            'occupation'      => $this->occupation,
            'marital_status'  => $this->marital_status,
            'relation'        => $this->relation,
        ];
    }
}
