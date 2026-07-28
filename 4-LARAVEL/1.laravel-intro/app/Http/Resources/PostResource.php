<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'body' => $this->body,
            'image_url' => $this->imageUrl(),
            'author' => $this->user->name ?? 'Tidak diketahui',
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
