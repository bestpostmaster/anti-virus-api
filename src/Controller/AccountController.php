<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccountController extends AbstractController
{
    /**
     * @Route("/create-account", name="app_create_account")
     */
    public function index(): Response
    {
        return $this->render('app/create-account.html.twig', [
        ]);
    }
}
