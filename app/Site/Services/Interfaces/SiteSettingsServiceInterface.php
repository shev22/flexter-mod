<?php

namespace App\Site\Services\Interfaces;

use App\Site\Data\SiteSettingsData;

interface SiteSettingsServiceInterface
{
    public function get(): SiteSettingsData;

    /**
     * @return array<string, mixed>
     */
    public function getPayload(): array;

    /**
     * @param  array<string, mixed>  $payload
     */
    public function update(array $payload): SiteSettingsData;
}
