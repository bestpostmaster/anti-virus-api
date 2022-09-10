<?php

declare(strict_types=1);

namespace App\Subscriber;

use App\Entity\Flood;
use App\Repository\FloodRepository;
use Doctrine\Persistence\ManagerRegistry;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationFailureEvent;

class AuthenticationFailureListener
{
    private ManagerRegistry $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * @param AuthenticationFailureEvent $event
     */
    public function onAuthenticationFailureResponse(AuthenticationFailureEvent $event): void
    {
        $this->removeOldFloods();
        $ip = $this->getIp();
        $maxTryInTenSeconds = 3;

        $manager = $this->doctrine->getManager();
        $floodsByIp = $manager->getRepository(Flood::class)->findBy(['ip' => $ip]);

        if($floodsByIp && count($floodsByIp) > $maxTryInTenSeconds) {
            throw new \Exception('Please dont spam our server! ');
        }

        $flood = new Flood();
        $flood->setIp($ip);
        $flood->setLastTry(time());
        $manager->persist($flood);
        $manager->flush($flood);
    }

    private function removeOldFloods(): void
    {
        $manager = $this->doctrine->getManager();
        $floodRepository = $manager->getRepository(Flood::class);
        $floodRepository -> removeOldFloods();
        $manager->flush();
    }

    private function getIp(): ?string
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }

        return $ip;
    }
}