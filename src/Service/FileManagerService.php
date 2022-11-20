<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\HostedFile;
use App\Entity\User;
use App\Repository\ActionRequestedRepository;
use App\Repository\HostedFileRepository;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\MailerInterface;

class FileManagerService
{
    private ManagerRegistry $doctrine;
    private string $hostingDirectory;
    private string $actionsResultsDirectory;
    private string $projectDirectory;
    private string $kernelEnvironment;
    private HostedFileRepository $hostedFileRepository;
    private MailerInterface $mailer;
    private string $webSiteEmailAddress;
    private LoggerInterface $logger;
    private ActionRequestedRepository $actionRequestedRepository;

    public function __construct(ManagerRegistry $doctrine, string $hostingDirectory,
                                string $actionsResultsDirectory, string $projectDirectory,
                                string $kernelEnvironment, HostedFileRepository $hostedFileRepository,
                                MailerInterface $mailer, string $webSiteEmailAddress, LoggerInterface $logger, ActionRequestedRepository $actionRequestedRepository)
    {
        $this->doctrine = $doctrine;
        $this->hostingDirectory = $hostingDirectory;
        $this->actionsResultsDirectory = $actionsResultsDirectory;
        $this->projectDirectory = $projectDirectory;
        $this->kernelEnvironment = $kernelEnvironment;
        $this->hostedFileRepository = $hostedFileRepository;
        $this->mailer = $mailer;
        $this->webSiteEmailAddress = $webSiteEmailAddress;
        $this->logger = $logger;
        $this->actionRequestedRepository = $actionRequestedRepository;
    }

    public function deleteUserFiles(User $user): bool
    {
        $files = $this->hostedFileRepository->findBy(['user' => $user->getId()]);

        foreach ($files as $file) {
            $this->deleteRelatedActions($file, $this->actionRequestedRepository);

            $manager = $this->doctrine->getManager();
            $manager->remove($file);
            $manager->flush();

            $fullPath = $this->hostingDirectory.$file->getName();

            if (file_exists($fullPath)) {
                unlink($this->hostingDirectory.$file->getName());
            }

            $this->decreaseUserSpace($user, $file->getSize());
        }

        return true;
    }

    private function decreaseUserSpace(User $user, float $sizeToDeduct): void
    {
        $manager = $this->doctrine->getManager();
        $user->setTotalSpaceUsedMo($user->getTotalSpaceUsedMo() - $sizeToDeduct);
        $manager->persist($user);
        $manager->flush($user);
    }

    public function deleteRelatedActions(HostedFile $file, ActionRequestedRepository $actionRepository)
    {
        $relatedActions = $actionRepository->findRelatedActions($file->getId());
        if (empty($relatedActions)) {
            return $file;
        }

        $actionRepository->deleteRelatedActions($file->getId());

        foreach ($relatedActions as $actionRequested) {
            $actionResultsDir = $this->actionsResultsDirectory.$actionRequested->getAction()->getActionName();
            if (is_dir($this->actionsResultsDirectory) && is_dir($actionResultsDir) && is_dir($actionResultsDir.DIRECTORY_SEPARATOR.$actionRequested->getId())) {
                $this->deleteDirectory($actionResultsDir.DIRECTORY_SEPARATOR.$actionRequested->getId());
            }
        }
    }

    private function deleteDirectory(string $dir): bool
    {
        $this->logger->info('Remove directory : '.$dir);

        if (!file_exists($dir)) {
            return true;
        }

        if (!is_dir($dir)) {
            return unlink($dir);
        }

        foreach (scandir($dir) as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }

            if (!$this->deleteDirectory($dir.DIRECTORY_SEPARATOR.$item)) {
                return false;
            }
        }

        return rmdir($dir);
    }
}
