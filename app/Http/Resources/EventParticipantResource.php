<?php
namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EventParticipantResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            //     'event_id',
            // 'head_of_family_id',
            // 'quantity',
            // 'total_price',
            // 'payment_status',
            'id'             => $this->id,
            'event'          => new EventResource($this->event),
            'head_of_family' => $this->headOfFamily ? new HeadOfFamilyResource($this->headOfFamily) : null,
            'quantity'       => $this->quantity,
            'total_price'    => (float) (string) $this->total_price,
            'payment_status' => $this->payment_status,
            'created_at'     => $this->created_at,
        ];
    }
}
