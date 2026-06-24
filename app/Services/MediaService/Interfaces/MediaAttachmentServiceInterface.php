<?php

namespace App\Services\MediaService\Interfaces;

interface MediaAttachmentServiceInterface
{
    /**
     * @param array $mediaCollection
     * @param string $mediaType
     * @return void
     */
    public function setMediaAttachments(array $mediaCollection, string $mediaType): void;

}
