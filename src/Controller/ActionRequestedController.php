<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\ActionRequestedRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;

class ActionRequestedController extends AbstractController
{
    private string $hostingDirectory;
    private string $projectDirectory;
    private string $actionsResultsDirectory;

    public function __construct(string $hostingDirectory, string $projectDirectory, string $actionsResultsDirectory)
    {
        $this->hostingDirectory = $hostingDirectory;
        $this->projectDirectory = $projectDirectory;
        $this->actionsResultsDirectory = $actionsResultsDirectory;
    }

    /**
     * @Route("/api/actions/get-action-infos/{actionId}", name="get_action_infos")
     */
    public function getActionInfos(Request $request, ActionRequestedRepository $actionRequestedRepository): Response
    {
        if (!$this->getUser()) {
            throw new \Exception('No user logged in');
        }

        $userId = $this->getUser()->getId();
        $id = $request->get('actionId');

        if (!$this->container->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            throw new \Exception('No user logged in');
        }

        if ($this->container->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            $result = $actionRequestedRepository->findOneBy(['id' => $id]);
        } else {
            $result = $actionRequestedRepository->findOneBy(['id' => $id, 'user' => $userId]);
        }

        if (!$result) {
            throw $this->createNotFoundException('The action does not exist');
        }

        return $this->json($result, 200, [], ['groups' => 'file:read']);
    }

    /**
     * @Route("/api/actions/download-action-result/{actionId}/{resultFileName}", name="download_action_result")
     */
    public function downloadActionResult(Request $request, ActionRequestedRepository $actionRequestedRepository): BinaryFileResponse
    {
        if (!$this->getUser()) {
            throw new \Exception('No user logged in');
        }

        $userId = $this->getUser()->getId();
        $resultFileName = $request->get('resultFileName');
        $actionId = $request->get('actionId');

        if (!$this->container->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            throw new \Exception('No user logged in');
        }

        if ($this->container->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            $action = $actionRequestedRepository->findOneBy(['id' => $actionId]);
        } else {
            $action = $actionRequestedRepository->findOneBy(['id' => $actionId, 'user' => $userId]);
        }

        if (!$action) {
            throw $this->createNotFoundException('The action does not exist');
        }

        if (!$action->isAccomplished()) {
            throw new \Exception('This action is not accomplished!');
        }

        $found = false;
        $actionResultFiles = $action->getActionResults();
        foreach ($actionResultFiles as $result) {
            if ($result === $resultFileName) {
                $found = $result;
            }
        }

        if (!$found) {
            throw $this->createNotFoundException('The result does not exist');
        }

        $directory = $this->actionsResultsDirectory.DIRECTORY_SEPARATOR.$action->getAction()->getActionName().DIRECTORY_SEPARATOR.$action->getId();
        $response = new BinaryFileResponse($directory.DIRECTORY_SEPARATOR.$found);

        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT
        );

        return $response;
    }
}
