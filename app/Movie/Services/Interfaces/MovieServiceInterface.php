<?php

namespace App\Movie\Services\Interfaces;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;


interface MovieServiceInterface
{
    /**
     * @return void
     */
    public function popular(): void;

    /**
     * @return void
     */
    public function nowPlaying(): void;

    /**
     * @return void
     */
    public function upcoming(): void;

    /**
     * @return void
     */
    public function topRated():void;

    /**
     * @return void
     */
    public function trending():void;

    /**
     * @return LengthAwarePaginator
     */
    public function getMovies(): LengthAwarePaginator;

    /**
     * @param string $movieId
     * @return array
     */
    public function getMovieWithRelatedMovies(string $movieId):array ;
}
