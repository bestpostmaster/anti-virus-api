<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccountController extends AbstractController
{
    /**
     * @Route(
     *     "/{_locale}/create-account",
     *     name="app_create_account",
     *     requirements={
     *         "_locale": "en|fr|de|es|zh|ar|hi|en",
     *     }
     * )
     */
    public function index(Request $request): Response
    {
        return $this->render('app/create-account.html.twig', [
            'lang' => $request->get('_locale')
        ]);
    }

    /**
     * @Route(
     *     "/{_locale}/private-space",
     *     name="private_space",
     *     requirements={
     *         "_locale": "en|fr|de|es|zh|ar|hi|en",
     *     }
     * )
     */
    public function privateSpace(Request $request): Response
    {
        return $this->render('app/private-space.html.twig', [
            'lang' => $request->get('_locale')
        ]);
    }
}
