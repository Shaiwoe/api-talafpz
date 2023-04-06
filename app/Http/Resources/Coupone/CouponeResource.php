<?php

namespace App\Http\Resources\Coupone;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CouponeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'percentage' => $this->percentage,
            'created_at' => verta($this->created_at)->formatDatetime(),
            'expired_at_jalali' => verta($this->expired_at)->formatDatetime(),
            'expired_at' => $this->expired_at,
        ];
    }
}
