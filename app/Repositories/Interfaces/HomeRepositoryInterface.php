<?php

namespace App\Repositories\Interfaces;

use Illuminate\Support\Collection;

interface HomeRepositoryInterface
{
    /**
     * @return array
     */
    public function loadHomePageMoviesData(): array;

    /**
     * @return array
     */
    public function loadHomePageTvData(): array;
}
