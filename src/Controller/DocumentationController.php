<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DocumentationController extends AbstractController
{
    /**
     * @Route(
     *     "/{_locale}/documentation",
     *     name="documentation",
     *     requirements={
     *         "_locale": "en|fr|de|es|zh|ar|hi|en",
     *     }
     * )
     */
    public function index(Request $request): Response
    {
        return $this->render('app/documentation.html.twig', [
            'lang' => $request->get('_locale'),
        ]);
    }
}
