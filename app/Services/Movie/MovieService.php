<?php

namespace App\Services\Movie;



use App\Models\Director;
use App\Models\Genre;
use App\Models\Movie;
use App\Models\Tmdb;
use App\Repositories\MovieRepository;
use Illuminate\Support\Collection;

class MovieService
{
    public function __construct(protected MovieRepository $movieRepository = new MovieRepository)
    {
    }

    public function UpdateOrCreate(array $movie)
    {
        //TODO need check through movie Model
        if (!Tmdb::where('tmdb_id', $movie['movie']['id'])) {
            $movieModel = $this->normalizeMovieData($movie);
            $director = Director::firstOrCreate(['tmdb_id' => $movie['director']['id']], ['name' => $movie['director']['name'], 'biography' => $movie['director']['biography'], 'birthday' => $movie['director']['birthday'], 'place_of_birth' => $movie['director']['place_of_birth'], 'tmdb_url' => $movie['director']['profile_path']]);
            $movieModel['director_id'] = $director->id;
            $movieModel['tmdb_id'] = Tmdb::updateOrCreate(['tmdb_id' => $movie['movie']['id']], ['vote_average' => $movie['movie']['vote_average'], 'vote_count' => $movie['movie']['vote_count'], 'url' => "https://www.themoviedb.org/movie/" . $movie['movie']['id']])->id;
            $movieModel = Movie::create($movieModel);
            foreach ($movie['movie_details']['genres'] as $genre) {
                $genre = Genre::firstOrCreate(['name' => $genre['name']]);
                //TODO need refactor
                $movieModel->genres()->syncWithoutDetaching($genre);
            }
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

}
