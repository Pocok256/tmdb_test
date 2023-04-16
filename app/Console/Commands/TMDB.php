<?php

namespace App\Console\Commands;

use App\Repositories\MovieRepository;
use App\Services\Movie\MovieService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Services\TMDBApi as TMDBServiceApi;

class TMDB extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tmdb:toprated';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tmdb = new TMDBServiceApi();
        $movies = $tmdb->getTopRatedMovies(config('services.tmdb.top_rated_results'));
        foreach ($movies as $movie) {
            $moviesDetails[$movie['id']] = ['movie' => $movie, 'movie_details' => $tmdb->getMovieDetailsById($movie['id']), 'director' => $tmdb->getDirectorDetailsByMovieId($movie['id'])];
        }
        foreach ($moviesDetails as $movie) {
            (new MovieService())->UpdateOrCreate($movie);
        }
    }
}
