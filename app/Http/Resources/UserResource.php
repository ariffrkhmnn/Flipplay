<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,

            // Hanya muncul kalau dipakai withCount('media')
            'collection_count' => $this->whenCounted('media'),

            // Hanya muncul kalau relasi 'media' sudah di-load (with/load)
            'media' => MediaResource::collection($this->whenLoaded('media')),
        ];
    }
}
