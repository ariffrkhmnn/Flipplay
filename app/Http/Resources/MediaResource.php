<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MediaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'type' => $this->type,
            'type_label' => $this->getTypeLabel(),
            'genre' => $this->genre,
            'year' => $this->year,
            'length' => $this->length,
            'length_label' => $this->getLengthLabel(),   // contoh: "28 episode"
            'action' => $this->getAction(),               // Tonton / Baca
            'rating' => $this->rating,
            'is_available' => $this->is_available,

            // Hanya muncul kalau Media diambil lewat relasi koleksi user (ada data pivot)
            'added_at' => $this->whenPivotLoaded('media_user', fn () => $this->pivot->created_at),

            // Hanya muncul kalau relasi 'users' sudah di-load (with/load)
            'users' => UserResource::collection($this->whenLoaded('users')),
        ];
    }
}
