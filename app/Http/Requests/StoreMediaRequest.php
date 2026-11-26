<?php

namespace App\Http\Requests;

use App\Models\Media;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

// Dipakai untuk POST (semua field wajib) dan PATCH/PUT (hanya field yang dikirim divalidasi).
class StoreMediaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $isPatch = $this->isMethod('PATCH');
        $required = $isPatch ? 'sometimes' : 'required';

        return [
            // unique, tapi saat update abaikan media yang sedang diedit sendiri
            'title' => [
                $required, 'string', 'max:255',
                Rule::unique('media', 'title')->ignore($this->route('media')),
            ],
            'type' => [$required, Rule::in(Media::TYPES)],
            'genre' => [$required, 'string', 'max:100'],
            'year' => [$required, 'integer', 'between:1900,2100'],
            'length' => [$required, 'integer', 'between:1,9999'],
            'rating' => ['nullable', 'numeric', 'between:0,10'],
            'is_available' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Judul media wajib diisi',
            'title.unique' => 'Judul media sudah ada, tidak boleh duplikat',
            'type.required' => 'Jenis media wajib diisi',
            'type.in' => 'Jenis media harus salah satu dari: anime, drama, manga, light_novel',
            'genre.required' => 'Genre wajib diisi',
            'year.required' => 'Tahun rilis wajib diisi',
            'year.between' => 'Tahun rilis harus antara 1900 sampai 2100',
            'length.required' => 'Jumlah episode/chapter/volume wajib diisi',
            'length.between' => 'Jumlah episode/chapter/volume harus antara 1 sampai 9999',
            'rating.between' => 'Rating harus antara 0 sampai 10',
            'rating.numeric' => 'Rating harus berupa angka',
            'is_available.boolean' => 'is_available harus true atau false',
        ];
    }
}
