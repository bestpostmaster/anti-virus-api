<?php

declare(strict_types=1);

namespace App\Subscriber;

use Doctrine\Persistence\ManagerRegistry;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;

class AuthenticationSuccessListener
{
    private ManagerRegistry $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function onAuthenticationSuccessResponse(AuthenticationSuccessEvent $event): void
    {
        if (!$event->getUser()->isEmailConfirmed()) {
            $event->stopPropagation();
            $event->setData(['error' => '0024', 'message' => 'You have not confirmed your email address. Please click on the link you received by email. Also check the spam folder']);
        }

        $event->setData([
            'token' => $event->getData()['token'],
            'userId' => $event->getUser()->getId(),
        ]);
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
