<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\AntiSpamToken;
use App\Repository\AntiSpamTokenRepository;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;

class AntiSpamTokenService
{
    private ManagerRegistry $doctrine;
    private LoggerInterface $logger;
    private AntiSpamTokenRepository $antiSpamTokenRepository;

    public function __construct(ManagerRegistry $doctrine, LoggerInterface $logger, AntiSpamTokenRepository $antiSpamTokenRepository)
    {
        $this->doctrine = $doctrine;
        $this->logger = $logger;
        $this->antiSpamTokenRepository = $antiSpamTokenRepository;
    }

    public function generateToken(): AntiSpamToken
    {
        $currentTime = new \DateTime('now', new \DateTimeZone('Europe/Paris'));
        $antiSpamToken = new AntiSpamToken();
        $antiSpamToken->setToken(md5(uniqid((string) mt_rand(), true)).md5(uniqid((string) mt_rand(), true)));
        $antiSpamToken->setCreationDate($currentTime);

        $manager = $this->doctrine->getManager();
        $manager->persist($antiSpamToken);
        $manager->flush($antiSpamToken);

        return $antiSpamToken;
    }

    public function tokenExists(string $token): bool
    {
        if (!$this->antiSpamTokenRepository->findOneBy(['token' => $token])) {
            $this->logger->info('Check : Invalid token');

            return false;
        }

        return true;
    }

    public function deleteToken(string $token): bool
    {
        $result = $this->antiSpamTokenRepository->findOneBy(['token' => $token]);

        if (!$result) {
            $this->logger->info('Delete : Invalid token');

            return false;
        }

        $manager = $this->doctrine->getManager();
        $manager->remove($result);
        $manager->flush();

        return true;
    }
}
