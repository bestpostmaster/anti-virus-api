<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class StatusController extends AbstractController
{
    private string $kernelEnvironment;

    public function __construct(string $kernelEnvironment)
    {
        $this->kernelEnvironment = $kernelEnvironment;
    }

    /**
     * @Route("/status", name="status")
     */
    public function getStatus(UserRepository $userRepository): Response
    {
        $admin = $userRepository->findOneBy(['login' => 'admin']);

        if (!$admin) {
            return new Response('ko');
        }

        return new Response('ok');
    }

    /**
     * @Route("/vmstat", name="vmstat")
     */
    public function showVmstat(): Response
    {
        // Remove this if you develop on Linux
        if ($this->kernelEnvironment === 'dev') {
            return new Response('1200');
        }

        exec('vmstat', $output, $retval);
        $values = $output[2];
        $values = str_replace('  ', ' ', $values);
        $values = str_replace('  ', ' ', $values);
        $free = explode(' ', $values)[5];

        return new Response($free);
    }

    /**
     * @Route(
     *     "/{_locale}/test-translator",
     *     name="contact",
     *     requirements={
     *         "_locale": "en|fr|de|es|zh|ar|hi|en",
     *     }
     * )
     */
    public function showTranslationMessage(TranslatorInterface $translator): Response
    {
        return new Response($translator->trans('Symfony is great'));
    }
}
