<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FrontController extends AbstractController
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
     *         "_locale": "en|fr|de|es|zh|ar|hi",
     *     }
     * )
     */
    public function allLanguagesHome(Request $request): Response
    {
        return $this->render('app/index.html.twig', [
            'lang' => $request->get('_locale'),
        ]);
    }

    /**
     * @Route(
     *     "/{_locale}/user/settings",
     *     name="user_setings",
     *     requirements={
     *         "_locale": "en|fr|de|es|zh|ar|hi",
     *     }
     * )
     */
    public function settings(Request $request): Response
    {
        return $this->render('app/settings.html.twig', [
            'lang' => $request->get('_locale'),
        ]);
    }

    /**
     * @Route(
     *     "/{_locale}/user/change-my-password",
     *     name="change_my_password",
     *     requirements={
     *         "_locale": "en|fr|de|es|zh|ar|hi",
     *     }
     * )
     */
    public function changeMyPassword(Request $request): Response
    {
        return $this->render('app/change-my-password.html.twig', [
            'lang' => $request->get('_locale'),
        ]);
    }

    /**
     * @Route(
     *     "/{_locale}/user/delete-my-account",
     *     name="delete_my_account",
     *     requirements={
     *         "_locale": "en|fr|de|es|zh|ar|hi",
     *     }
     * )
     */
    public function deleteMyAccount(Request $request): Response
    {
        return $this->render('app/delete-my-account.html.twig', [
            'lang' => $request->get('_locale'),
        ]);
    }

    /**
     * @Route(
     *     "/{_locale}/user/delete-my-account-confirmation/{token}",
     *     name="delete_my_account_confirmation",
     *     requirements={
     *         "_locale": "en|fr|de|es|zh|ar|hi",
     *     }
     * )
     */
    public function deleteMyAccountConfirmation(Request $request, UserRepository $userRepository): Response
    {
        $user = $userRepository->findOneBy(['secretTokenForValidation' => $request->get('token'), 'deleteAccountRequested' => true]);
        $found = true;

        if(!$user) {
            $found = false;
        }

        return $this->render('app/delete-my-account-confirmation.html.twig', [
            'lang' => $request->get('_locale'),
            'found' => $found
        ]);
    }
}
