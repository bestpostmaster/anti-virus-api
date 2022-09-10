<?php

declare(strict_types=1);

namespace App\Service;

class FileConverterService
{
    public function convert(int $fileId, string $to): string
    {
        return 'result'.$to;
    }
}
