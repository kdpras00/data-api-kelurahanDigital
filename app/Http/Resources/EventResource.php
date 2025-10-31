<?php
namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EventResource extends JsonResource
{

    public function toArray(Request $request): array
    {
        return [
            'id'                 => $this->id,
            'thumbnail'          => asset('storage/' . $this->thumbnail),
            'name'               => $this->name,
            'description'        => $this->description,
            'price'              => (float) (string) $this->price,
            'date'               => $this->date,
            'time'               => $this->time,
            'is_active'          => $this->is_active,
            'event_participants' => EventParticipantResource::collection($this->whenLoaded('eventParticipants')),
        ];
    }
}
