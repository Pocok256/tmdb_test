<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tmdb extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $appends = ['tmdb_url'];

    public function getTmdbUrlAttribute(): string
    {
        return "https://www.themoviedb.org/movie/{$this->id}";
    }
}
