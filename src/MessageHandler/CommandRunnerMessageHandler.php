<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Entity\ActionRequested;
use App\Message\CommandRunnerMessage;
use App\Service\VirusScannerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class CommandRunnerMessageHandler implements MessageHandlerInterface
{
    private EntityManagerInterface $em;
    private VirusScannerService $virusScannerService;

    public function __construct(EntityManagerInterface $em, VirusScannerService $virusScannerService)
    {
        $this->em = $em;
        $this->virusScannerService = $virusScannerService;
    }

    public function __invoke(CommandRunnerMessage $message)
    {
        $actionRequested = $this->em->find(ActionRequested::class, $message->getActionRequestedId());

        switch ($actionRequested->getActionName()) {
            case 'Scan':
                $this->virusScannerService->runCommand($actionRequested);
                break;
        }
    }
}
