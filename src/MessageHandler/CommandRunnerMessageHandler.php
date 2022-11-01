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
    private string $kernelEnvironment;

    public function __construct(EntityManagerInterface $em, VirusScannerService $virusScannerService, string $kernelEnvironment)
    {
        $this->em = $em;
        $this->virusScannerService = $virusScannerService;
        $this->kernelEnvironment = $kernelEnvironment;
    }

    public function getFreeMemory(): int
    {
        // Remove this if you develop on Linux
        if ($this->kernelEnvironment === 'dev') {
            return 1200;
        }

        exec('vmstat -S M', $output, $retval);
        $values = $output[2];
        $values = str_replace(['  ', '  '], ' ', $values);
        $free = explode(' ', $values)[5];

        return (int) $free;
    }

    public function __invoke(CommandRunnerMessage $message)
    {
        if ($this->getFreeMemory() < 1000) {
            throw new \Exception('Free memory < 1G !');
        }

        $actionRequested = $this->em->find(ActionRequested::class, $message->getActionRequestedId());

        if (!$actionRequested) {
            return;
        }

        switch ($actionRequested->getAction()->getActionName()) {
            case 'Scan':
                echo 'Action Id '.$actionRequested->getId().' '.$actionRequested->getAction()->getActionName().'.. ';
                $this->virusScannerService->runCommand($actionRequested);
                break;
        }
    }
}
