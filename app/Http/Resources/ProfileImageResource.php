<?php
namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfileImageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'image' => asset('storage/' . $this->image),
        ];
    }
}
