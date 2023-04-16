<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class TMDBApi
{
    //TODO Add custom Request method to all HTTP get requests

    public function getTopRatedMovies(int $count = 20): array
    {
        $result = [];
        $page = 1;
        while (count($result) < $count) {
            $response = $this->getTopRatedMoviesByPage($page);
            $result = array_merge($result, $response['results']);
            $page++;
        }
        return array_slice($result, 0, $count);
    }

    public function getTopRatedMoviesByPage(int $page = 1)
    {
        $response = Http::get(config('services.tmdb.endpoint') . '/movie/top_rated', [
            'api_key' => config('services.tmdb.api'),
            'page' => $page
        ]);
        if ($response->ok()) {
            return $response->json();
        }
        //TODO Add ExceptionHandling
        return [];
    }

    public function getMovieDetailsById(int $movieId)
    {
        $response = Http::get(config('services.tmdb.endpoint') . '/movie/' . $movieId, [
            'api_key' => config('services.tmdb.api'),
        ]);
        if ($response->ok()) {
            return $response->json();
        }
        //TODO Add ExceptionHandling
        return [];
    }

    public function getDirectorByCredits(array $credits)
    {
        return collect($credits)->where('job', 'Director')->first();
    }

    public function getCreditsById(int $movieId)
    {
        $response = Http::get(config('services.tmdb.endpoint') . '/movie/' . $movieId . '/credits', [
            'api_key' => config('services.tmdb.api'),
        ]);
        if ($response->ok()) {
            return $response->json();
        }
        //TODO Add ExceptionHandling
        return [];
    }

    public function getDirectorDetailsByMovieId(int $movieId)
    {
        $credits = $this->getCreditsById($movieId);
        $director = $this->getDirectorByCredits($credits['crew']);
        if ($director) {
            $response = Http::get(config('services.tmdb.endpoint') . '/person/' . $director['id'], [
                'api_key' => config('services.tmdb.api'),
            ]);
            if ($response->ok()) {
                return $response->json();
            }
        }
        //TODO Add ExceptionHandling
        return [];
    }
}
