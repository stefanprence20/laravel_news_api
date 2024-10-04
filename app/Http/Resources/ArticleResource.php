<?php

namespace App\Http\Resources;

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
            'content' => $this->content,
            'authors' => AuthorResource::collection($this->whenLoaded('authors')),
            'url' => $this->url,
            'published_at' => $this->published_at,
            'source' => new SourceResource($this->whenLoaded('source')),
        ];
    }
}
