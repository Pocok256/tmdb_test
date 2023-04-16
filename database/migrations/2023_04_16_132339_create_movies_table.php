<?php

use App\Models\Director;
use App\Models\Tmdb;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('movies', function (Blueprint $table) {
            $table->id();
            $table->string('title')->unique();
            $table->smallInteger('length');
            $table->date('release_date');
            $table->longText('overview');
            $table->string('poster_url');
            $table->foreignIdFor(Director::class);
            $table->foreignIdFor(Tmdb::class);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movies');
    }
};
