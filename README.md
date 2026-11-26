# FlipPlay API

REST API FlipPlay (Laravel 12) — kelanjutan prototipe terminal OOP di Modul 3.
Class `Media` (Anime, Drama, Manga, Light Novel) dan `User` kini menjadi Model Eloquent yang tersimpan di database,
lengkap dengan Migration, Controller, Routing, Validasi, dan relasi many-to-many (koleksi media milik pengguna).

> Praktikum Pemrograman Web — Modul 4 (Laravel REST API: CRUD, Eloquent ORM & Relationship)

## Fitur

- CRUD Media (index, show, store, update, destroy) dengan pagination, eager loading, dan filter (`search`, `type`, `genre`, `available`)
- Relasi **many-to-many** `Media` <-> `User` lewat pivot table `media_user` (koleksi pengguna)
- Form Request Validation (pesan error Bahasa Indonesia, status 422); judul media wajib diisi dan tidak boleh duplikat
- API Resource dengan `whenLoaded` agar format JSON konsisten
- Custom action koleksi: tambah (attach), lihat, dan hapus (detach) media dari koleksi pengguna
- Aturan dari Modul 3 tetap berlaku: rating 0–10, media "segera hadir" (`is_available = false`) tidak bisa masuk koleksi, media tidak boleh masuk koleksi dua kali

## Struktur Database

```
users                      media_user (pivot)               media
-----                      ------------------               -----
id                         id                               id
name                       user_id  -> users.id             title (unique)
email (unique)             media_id -> media.id             type (anime|drama|manga|light_novel)
...                        timestamps                       genre
                           unique(user_id, media_id)        year
                                                            length (episode/chapter/volume)
                                                            rating (0-10)
                                                            is_available
```

## Instalasi

Kebutuhan: PHP 8.2+, Composer, MySQL (Laragon/XAMPP).

```bash
git clone <https://github.com/ariffrkhmnn/Flipplay.git>
cd <nama-folder>
composer install
copy .env.example .env        # Linux/Mac: cp .env.example .env
php artisan key:generate
```

Buat database kosong bernama `flipplay_api` (HeidiSQL / phpMyAdmin), lalu cek konfigurasi `.env`:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=flipplay_api
DB_USERNAME=root
DB_PASSWORD=
```

Jalankan migration + seeder, lalu server:

```bash
php artisan migrate:fresh --seed
php artisan route:list
php artisan serve
```

API tersedia di `http://127.0.0.1:8000/api`. Seeder mengisi 3 user dan 6 media (data yang sama dengan Modul 3);
user pertama sudah punya 2 media di koleksinya.

## Daftar Endpoint

| Method | Endpoint | Fungsi | Status sukses |
|---|---|---|---|
| GET | `/api/media` | Daftar media (`?search=`, `?type=`, `?genre=`, `?available=`) | 200 |
| GET | `/api/media/{media}` | Detail media beserta pengguna yang mengoleksinya | 200 |
| POST | `/api/media` | Tambah media | 201 |
| PATCH/PUT | `/api/media/{media}` | Ubah media | 200 |
| DELETE | `/api/media/{media}` | Hapus media | 200 |
| GET | `/api/users` | Daftar pengguna | 200 |
| GET | `/api/users/{user}` | Detail pengguna beserta koleksinya | 200 |
| POST | `/api/users` | Tambah pengguna | 201 |
| GET | `/api/users/{user}/collection` | Koleksi media milik pengguna | 200 |
| POST | `/api/users/{user}/collection` | Tambah media ke koleksi (`{"media_id": 1}`) | 201 |
| DELETE | `/api/users/{user}/collection/{media}` | Hapus media dari koleksi | 200 |

Kode error yang dipakai: `404` data tidak ditemukan, `409` konflik (media sudah ada di koleksi / belum tersedia), `422` validasi gagal.

### Contoh

`POST /api/media`
```json
{ "title": "Solo Leveling", "type": "manga", "genre": "Action", "year": 2018, "length": 200, "rating": 8.9 }
```

Respons `201`:
```json
{
  "data": {
    "id": 7,
    "title": "Solo Leveling",
    "type": "manga",
    "type_label": "Manga",
    "genre": "Action",
    "year": 2018,
    "length": 200,
    "length_label": "200 chapter",
    "action": "Baca",
    "rating": 8.9,
    "is_available": true
  }
}
```

Respons `422` (judul kosong):
```json
{
  "message": "Judul media wajib diisi",
  "errors": { "title": ["Judul media wajib diisi"] }
}
```

`POST /api/users/2/collection` dengan `{"media_id": 2}` dua kali -> kedua kalinya `409`:
```json
{ "message": "Media sudah ada di koleksi" }
```

## Pengujian (Postman)

Import `postman/FlipPlay-API.postman_collection.json` ke Postman. Koleksi berisi happy path dan failure path,
tiap request sudah punya test status code. Jalankan lewat **Runner** setelah `php artisan migrate:fresh --seed`.

## Struktur Kode Penting

```
app/Models/Media.php, User.php                    Model + relasi belongsToMany
app/Http/Controllers/MediaController.php          CRUD Media
app/Http/Controllers/UserController.php           User + custom action koleksi
app/Http/Requests/StoreMediaRequest.php           Validasi POST/PATCH Media
app/Http/Resources/MediaResource.php, UserResource.php   Format JSON (whenLoaded)
database/migrations/                              media, media_user (pivot)
database/seeders/                                 UserSeeder, MediaSeeder
routes/api.php                                    Seluruh endpoint
```
