<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Entity\HostedFile;
use App\Message\VirusScannerMessage;
use App\Service\VirusScannerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class VirusScannerMessageHandler implements MessageHandlerInterface
{
    private EntityManagerInterface $em;
    private VirusScannerService $virusScannerService;

    public function __construct(EntityManagerInterface $em, VirusScannerService $virusScannerService)
    {
        $this->em = $em;
        $this->virusScannerService = $virusScannerService;
    }

    public function __invoke(VirusScannerMessage $message)
    {
        $hostedFile = $this->em->find(HostedFile::class, $message->getFileId());
        echo "\n >>SCAN... File id : ".$message->getFileId()."\n";
        $this->virusScannerService->scan($hostedFile);
        echo "\n END. \n";
    }
}
