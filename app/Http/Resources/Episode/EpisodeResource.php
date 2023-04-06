<?php

namespace App\Http\Resources\Episode;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EpisodeResource extends JsonResource
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
            'course_id' => $this->course_id,
            'type' => $this->type,
            'tags' => $this->tags,
            'description' => $this->description,
            'body' => $this->body,
            'video' => url(env('EPISODE_VIDEO_UPLOAD_PATH') . $this->video),
            'status' => $this->status,
            'number' => $this->number,
        ];
    }
}
