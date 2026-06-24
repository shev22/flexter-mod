<?php

namespace App\Site\Services\Interfaces;

use App\Site\Data\SiteSettingsData;

interface SiteSettingsServiceInterface
{
    public function get(): SiteSettingsData;

    /**
     * @param  array<string, mixed>  $payload
     */
    public function update(array $payload): SiteSettingsData;
}
