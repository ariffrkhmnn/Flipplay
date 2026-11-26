<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMediaRequest;
use App\Http\Resources\MediaResource;
use App\Models\Media;
use Illuminate\Http\Request;

class MediaController extends Controller
{
    /**
     * GET /api/media
     * Daftar media + pengguna yang memasukkannya ke koleksi (eager loading).
     * Filter opsional: ?search=judul &type=anime &genre=Action &available=1
     */
    public function index(Request $request)
    {
        $media = Media::with('users')
            ->when($request->filled('search'), fn ($q) => $q->where('title', 'like', '%' . $request->search . '%'))
            ->when($request->filled('type'), fn ($q) => $q->where('type', $request->type))
            ->when($request->filled('genre'), fn ($q) => $q->where('genre', $request->genre))
            ->when($request->has('available'), fn ($q) => $q->where('is_available', $request->boolean('available')))
            ->orderBy('title')
            ->paginate(10)
            ->withQueryString();

        return MediaResource::collection($media);
    }

    /**
     * GET /api/media/{media}
     * Route Model Binding: 404 otomatis kalau id tidak ada.
     */
    public function show(Media $media)
    {
        return new MediaResource($media->load('users'));
    }

    /**
     * POST /api/media  -> 201 Created
     */
    public function store(StoreMediaRequest $request)
    {
        $media = Media::create($request->validated());

        return (new MediaResource($media))->response()->setStatusCode(201);
    }

    /**
     * PATCH|PUT /api/media/{media}
     */
    public function update(StoreMediaRequest $request, Media $media)
    {
        $media->update($request->validated());

        return new MediaResource($media->load('users'));
    }

    /**
     * DELETE /api/media/{media}
     * Baris di pivot media_user ikut terhapus (cascadeOnDelete di migration).
     */
    public function destroy(Media $media)
    {
        $media->delete();

        return response()->json(['message' => 'Media berhasil dihapus']);
    }
}
