<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Pivot table many-to-many: koleksi media milik pengguna.
// Nama mengikuti konvensi Laravel: singular, urut alfabet (media_user).
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('media_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('media_id')->constrained('media')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            // Satu media hanya boleh masuk koleksi satu user satu kali (pengaman di level database)
            $table->unique(['media_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media_user');
    }
};
