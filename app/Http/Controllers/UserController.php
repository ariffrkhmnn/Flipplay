<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Resources\MediaResource;
use App\Http\Resources\UserResource;
use App\Models\Media;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * GET /api/users
     */
    public function index()
    {
        $users = User::withCount('media')->paginate(10);

        return UserResource::collection($users);
    }

    /**
     * GET /api/users/{user}  -> user beserta koleksi mediannya
     */
    public function show(User $user)
    {
        return new UserResource($user->load('media'));
    }

    /**
     * POST /api/users  -> 201 Created
     */
    public function store(StoreUserRequest $request)
    {
        $user = User::create($request->validated());

        return (new UserResource($user))->response()->setStatusCode(201);
    }

    // ---------------------------------------------------------------
    // CUSTOM ACTION di luar CRUD standar: mengubah RELASI (pivot media_user)
    // ---------------------------------------------------------------

    /**
     * GET /api/users/{user}/collection
     * Koleksi media milik satu pengguna.
     */
    public function collection(User $user)
    {
        return MediaResource::collection(
            $user->media()->orderBy('title')->paginate(10)
        );
    }

    /**
     * POST /api/users/{user}/collection   body: { "media_id": 1 }
     * Tambah media ke koleksi (attach). Aturan sama seperti Modul 3:
     * media yang belum tersedia atau sudah ada di koleksi ditolak.
     */
    public function addToCollection(Request $request, User $user)
    {
        $validated = $request->validate([
            'media_id' => 'required|integer|exists:media,id',
        ], [
            'media_id.required' => 'media_id wajib diisi',
            'media_id.exists' => 'Media dengan media_id tersebut tidak ditemukan',
        ]);

        $media = Media::findOrFail($validated['media_id']);

        if (!$media->is_available) {
            return response()->json(['message' => 'Media belum tersedia (segera hadir)'], 409);
        }

        if ($user->media()->where('media.id', $media->id)->exists()) {
            return response()->json(['message' => 'Media sudah ada di koleksi'], 409);
        }

        $user->media()->attach($media->id);

        return response()->json([
            'message' => 'Media berhasil ditambahkan ke koleksi',
            'data' => new MediaResource($media),
        ], 201);
    }

    /**
     * DELETE /api/users/{user}/collection/{media}
     * Hapus media dari koleksi (detach). Data Media-nya sendiri tidak ikut terhapus.
     */
    public function removeFromCollection(User $user, Media $media)
    {
        if (!$user->media()->where('media.id', $media->id)->exists()) {
            return response()->json(['message' => 'Media tidak ada di koleksi pengguna ini'], 404);
        }

        $user->media()->detach($media->id);

        return response()->json(['message' => 'Media berhasil dihapus dari koleksi']);
    }
}
