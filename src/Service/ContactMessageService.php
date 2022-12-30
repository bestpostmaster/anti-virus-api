<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\ContactMessage;
use App\Repository\ContactMessageRepository;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;

class ContactMessageService
{
    private ManagerRegistry $doctrine;
    private LoggerInterface $logger;
    private ContactMessageRepository $contactMessageRepository;

    public function __construct(ManagerRegistry $doctrine, LoggerInterface $logger, ContactMessageRepository $contactMessageRepository)
    {
        $this->doctrine = $doctrine;
        $this->logger = $logger;
        $this->contactMessageRepository = $contactMessageRepository;
    }

    public function addMessage(string $senderEmail, string $message, string $ip): ContactMessage
    {
        $currentTime = new \DateTime('now', new \DateTimeZone('Europe/Paris'));
        $contactMessage = new ContactMessage();
        $contactMessage->setMessage($message);
        $contactMessage->setSenderEmail($senderEmail);
        $contactMessage->setSendingDate($currentTime);
        $contactMessage->setSenderIp($ip);

        $manager = $this->doctrine->getManager();
        $manager->persist($contactMessage);
        $manager->flush($contactMessage);

        return $contactMessage;
    }

    public function isSpammer(string $ip): bool
    {
        $result = $this->contactMessageRepository->getLastMessages($ip);

        if (count($result) < 5) {
            return false;
        }

        $this->logger->info('Spam detected, IP : '.$ip);

        return true;
    }
}
