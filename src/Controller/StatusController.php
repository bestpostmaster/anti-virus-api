<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StatusController extends AbstractController
{
    /**
     * @Route("/status", name="status")
     */
    public function getStatus(UserRepository $userRepository): Response
    {
        $admin = $userRepository->findOneBy(['login'=>'admin']);

        if(!$admin) {
            return new Response('ko');
        }

        return new Response('ok');
    }
}
