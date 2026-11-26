<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

// Kelanjutan class Media di Modul 3 (Anime/Drama/Manga/LightNovel),
// sekarang jadi satu Model Eloquent dengan kolom `type` sebagai pembeda jenis.
class Media extends Model
{
    public const TYPES = ['anime', 'drama', 'manga', 'light_novel'];

    // Eksplisit, karena "media" dianggap bentuk jamak oleh Laravel
    protected $table = 'media';

    protected $fillable = ['title', 'type', 'genre', 'year', 'length', 'rating', 'is_available'];

    // Nilai default saat Media dibuat lewat API tanpa mengirim kolom ini
    protected $attributes = [
        'rating' => 0,
        'is_available' => true,
    ];

    protected $casts = [
        'year' => 'integer',
        'length' => 'integer',
        'rating' => 'float',
        'is_available' => 'boolean',
    ];

    /**
     * Relasi many-to-many: satu Media bisa ada di koleksi banyak User.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }

    /**
     * Query scope: hanya media yang sudah tersedia (bukan "segera hadir").
     */
    public function scopeAvailable(Builder $query): Builder
    {
        return $query->where('is_available', true);
    }

    // Polymorphism ala Modul 3, sekarang berdasarkan kolom `type`
    public function getTypeLabel(): string
    {
        return match ($this->type) {
            'anime' => 'Anime',
            'drama' => 'Drama',
            'manga' => 'Manga',
            'light_novel' => 'Light Novel',
            default => 'Media',
        };
    }

    public function getAction(): string
    {
        return match ($this->type) {
            'anime', 'drama' => 'Tonton',
            'manga', 'light_novel' => 'Baca',
            default => 'Buka',
        };
    }

    public function getLengthLabel(): string
    {
        $unit = match ($this->type) {
            'anime', 'drama' => 'episode',
            'manga' => 'chapter',
            'light_novel' => 'volume',
            default => 'bagian',
        };

        return "{$this->length} {$unit}";
    }
}
