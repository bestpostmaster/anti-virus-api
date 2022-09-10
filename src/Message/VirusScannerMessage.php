<?php

declare(strict_types=1);

namespace App\Message;

final class VirusScannerMessage
{
    private int $fileId;

    public function __construct(int $fileId)
    {
        $this->fileId = $fileId;
    }

    public function getFileId(): int
    {
        return $this->fileId;
    }
}
