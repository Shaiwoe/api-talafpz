<?php

namespace App\Http\Resources\Article;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ArticleResource extends JsonResource
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
            'tags' => $this->tags,
            'description' => $this->description,
            'body' => $this->body,
            'image' => url(env('ARTICLE_IMAGES_UPLOAD_PATH') . $this->image),
            'video' => url(env('ARTICLE_VIDEO_UPLOAD_PATH') . $this->video),
            'voice' => url(env('ARTICLE_VOICE_UPLOAD_PATH') . $this->voice),
            'status' => $this->status,
        ];
    }
}
