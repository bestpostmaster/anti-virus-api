<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\AntiSpamTokenService;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AntiSpamController extends AbstractController
{
    private LoggerInterface $logger;
    private AntiSpamTokenService $antiSpamTokenService;

    public function __construct(LoggerInterface $logger, AntiSpamTokenService $antiSpamTokenService)
    {
        $this->logger = $logger;
        $this->antiSpamTokenService = $antiSpamTokenService;
    }

    /**
     * @Route("/api/security/get-public-token", name="get-public-token")
     */
    public function getToken(Request $request): Response
    {
        $this->logger->info('Try to generate token...');
        $this->antiSpamTokenService->generateToken();

        return $this->json($this->antiSpamTokenService->generateToken());
    }

    /**
     * @Route("/api/security/check-public-token/{token}", name="check-public-token")
     */
    public function checkToken(Request $request): Response
    {
        return $this->json(['valid' => $this->antiSpamTokenService->tokenExists($request->get('token'))]);
    }

    /**
     * @Route("/api/security/delete-public-token/{token}", name="delete-public-token")
     */
    public function deleteToken(Request $request): Response
    {
        $found = $this->antiSpamTokenService->deleteToken($request->get('token'));

        return $this->json(['deleted' => 'ok', 'found' => $found]);
    }
}
