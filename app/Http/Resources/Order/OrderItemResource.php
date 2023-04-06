<?php

namespace App\Http\Resources\Order;

use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
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
            'course_image' => url(env('COURSE_IMAGES_UPLOAD_PATH') . Course::find($this->course_id)->image),
            'course_name' => Course::find($this->course_id)->title,
            'price' => $this->price,
            'quantity' => $this->quantity,
            'subtotal' => $this->subtotal
        ];
    }
}
