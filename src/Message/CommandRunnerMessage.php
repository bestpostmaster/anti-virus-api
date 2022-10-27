<?php

declare(strict_types=1);

namespace App\Message;

final class CommandRunnerMessage
{
    private int $actionRequestedId;

    public function __construct(int $actionRequestedId)
    {
        $this->actionRequestedId = $actionRequestedId;
    }

    public function getActionRequestedId(): int
    {
        return $this->actionRequestedId;
    }
}
