<?php

namespace App\Http\Resources\Course;

use App\Http\Resources\Episode\EpisodeResource;
use App\Models\Skill;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseResource extends JsonResource
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
            'title' => $this->title,
            'slug' => $this->slug,
            'skill' => Skill::find($this->skill_id)->name,
            'skill_id' => $this->skill_id,
            'type' => $this->type,
            'description' => $this->description,
            'body' => $this->body,
            'image' => url(env('COURSE_IMAGES_UPLOAD_PATH') . $this->image),
            'status_value' => $this->getRawOriginal('status'),
            'status' => $this->status,
            'timeCourse' => $this->timeCourse,
            'condition' => $this->condition,
            'price' => $this->price,
            'quantity' => $this->quantity,
            'is_sale' => $this->is_sale,
            'sale_price' => $this->sale_price,
            'date_on_sale_from_jalali' => verta($this->date_on_sale_from)->formatDatetime(),
            'date_on_sale_to_jalali' => verta($this->date_on_sale_to)->formatDatetime(),
            'date_on_sale_from' => $this->date_on_sale_from,
            'date_on_sale_to' => $this->date_on_sale_to,
            'episodes' => EpisodeResource::collection($this->whenLoaded('episodes'))
        ];
    }
}
