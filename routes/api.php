<?php

use App\Http\Controllers\MediaController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// CRUD Media: index, show, store, update, destroy
// parameters(): Laravel mengubah "media" jadi "medium" untuk nama parameter route,
// sehingga Route Model Binding gagal. Ditetapkan manual supaya tetap {media}.
Route::apiResource('media', MediaController::class)->parameters(['media' => 'media']);

// User: cukup lihat daftar, detail, dan tambah user
Route::apiResource('users', UserController::class)->only(['index', 'show', 'store']);

// Custom action koleksi (relasi many-to-many): di luar apiResource
Route::get('/users/{user}/collection', [UserController::class, 'collection']);
Route::post('/users/{user}/collection', [UserController::class, 'addToCollection']);
Route::delete('/users/{user}/collection/{media}', [UserController::class, 'removeFromCollection']);
