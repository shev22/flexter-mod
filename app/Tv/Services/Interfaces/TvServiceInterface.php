<?php

namespace App\Tv\Services\Interfaces;

use App\Shared\Data\MediaFilterData;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface TvServiceInterface
{
    /**
     * @return void
     */
    public function popular(): void;

    /**
     * @return void
     */
    public function onTheAir(): void;

    /**
     * @return void
     */
    public function topRated():void;

    /**
     * @return void
     */
    public function trending():void;

    /**
     * @return void
     */
    public function AiringToday(): void;

    /**
     * @return LengthAwarePaginator
     */
    public function getTv(MediaFilterData $filter): LengthAwarePaginator;

    /**
     * @param string $tvId
     * @return array
     */
    public function getTvWithRelatedTv(string $tvId):array ;
}
