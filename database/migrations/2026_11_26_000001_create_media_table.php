<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('media', function (Blueprint $table) {
            $table->id();
            $table->string('title')->unique();                  // judul tidak boleh duplikat
            $table->string('type', 20)->index();                // anime | drama | manga | light_novel
            $table->string('genre', 100);
            $table->unsignedSmallInteger('year');               // tahun rilis
            $table->unsignedSmallInteger('length');             // episode / chapter / volume (tergantung type)
            $table->decimal('rating', 3, 1)->default(0);        // 0.0 - 10.0
            $table->boolean('is_available')->default(true);     // false = "segera hadir"
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media');
    }
};
