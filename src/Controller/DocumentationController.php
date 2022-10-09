<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DocumentationController extends AbstractController
{
    /**
     * @Route("/documentation", name="documentation")
     */
    public function index(): Response
    {
        return $this->render('app/documentation.html.twig', [
        ]);
    }
}
