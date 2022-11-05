<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="app_home")
     */
    public function index(): Response
    {
        return $this->redirectToRoute('english_home', [], 301);
    }

    /**
     * @Route("/en/", name="english_home")
     */
    public function englishHome(): Response
    {
        return $this->render('app/index.html.twig', [
            'lang' => 'en',
        ]);
    }

    /**
     * @Route(
     *     "/{_locale}/",
     *     name="all_languages_home",
     *     requirements={
     *         "_locale": "en|fr|de|es|zh|ar|hi|en",
     *     }
     * )
     */
    public function allLanguagesHome(Request $request): Response
    {
        return $this->render('app/index.html.twig', [
            'lang' => $request->get('_locale'),
        ]);
    }
}
