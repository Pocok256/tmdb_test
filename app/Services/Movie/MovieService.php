<?php

namespace App\Services\Movie;



use App\Http\Requests\DirectorRequest;
use App\Http\Requests\GenreRequest;
use App\Http\Requests\TmdbRequest;
use App\Models\Director;
use App\Models\Genre;
use App\Models\Movie;
use App\Models\Tmdb;
use App\Repositories\MovieRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class MovieService
{
    public function __construct(protected MovieRepository $movieRepository = new MovieRepository)
    {
    }

    public function UpdateOrCreate(array $movie)
    {
        //TODO need check through movie Model
        if (!Tmdb::where('tmdb_id', $movie['movie']['id'])->exists()) {
            DB::transaction(function () use ($movie) {
                $movieModel = $this->normalizeMovieData($movie);
                $director = $this->firstOrCreateDirector($movie['director']);
                $movieModel['director_id'] = $director->id;
                $movieModel['tmdb_id'] = $this->updateOrCreateTmdb($movie);
                $movieModel = Movie::create($movieModel);
                $this->saveGenre($movie['movie_details']['genres'], $movieModel);
            });
        }
    }

    public function normalizeMovieData(array $movie): array
    {
        $movieModel['title'] = $movie['movie']['original_title'];
        $movieModel['length'] = $movie['movie_details']['runtime'];
        $movieModel['release_date'] = $movie['movie']['release_date'];
        $movieModel['overview'] = $movie['movie_details']['overview'];
        $movieModel['poster_url'] = $movie['movie']['poster_path'];
        return $movieModel;
    }

    public function updateOrCreateTmdb(array $movie)
    {
        $tmdbData = Validator::validate($movie['movie'], (new TmdbRequest())->rules());
        $tmdbData['tmdb_id'] = $tmdbData['id'];
        $tmdb = Tmdb::updateOrCreate(['tmdb_id' => $tmdbData['tmdb_id']],$tmdbData);
        return $tmdb->id;
    }

    function firstOrCreateDirector($director)
    {
        $directorData = Validator::validate($director, (new DirectorRequest())->rules());
        //TODO need to extract this logic to DirectorRepository
        $directorData['tmdb_id'] = $directorData['id'];
        return Director::firstOrCreate(['tmdb_id' => $directorData['tmdb_id']],$directorData);
    }

    /**
     * @param $genres
     * @param $movieModel
     * @return void
     */
    function saveGenre($genres, $movieModel): void
    {
        foreach ($genres as $genre) {
            $genreData = Validator::validate($genre, (new GenreRequest())->rules());
            $genre = Genre::firstOrCreate($genreData);
            //TODO need refactor
            $movieModel->genres()->syncWithoutDetaching($genre);
        }
    }

}
