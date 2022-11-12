<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\ActionRequested;
use App\Repository\HostedFileRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

class VirusScannerService
{
    private ManagerRegistry $doctrine;
    private string $hostingDirectory;
    private string $actionsResultsDirectory;
    private string $projectDirectory;
    private string $kernelEnvironment;
    private HostedFileRepository $hostedFileRepository;
    private MailerInterface $mailer;
    private string $webSiteEmailAddress;

    public function __construct(ManagerRegistry $doctrine, string $hostingDirectory,
                                string $actionsResultsDirectory, string $projectDirectory,
                                string $kernelEnvironment, HostedFileRepository $hostedFileRepository,
                                MailerInterface $mailer, string $webSiteEmailAddress)
    {
        $this->doctrine = $doctrine;
        $this->hostingDirectory = $hostingDirectory;
        $this->actionsResultsDirectory = $actionsResultsDirectory;
        $this->projectDirectory = $projectDirectory;
        $this->kernelEnvironment = $kernelEnvironment;
        $this->hostedFileRepository = $hostedFileRepository;
        $this->mailer = $mailer;
        $this->webSiteEmailAddress = $webSiteEmailAddress;
    }

    public function runCommand(ActionRequested $actionRequested): void
    {
        $hostedFiles = $actionRequested->getHostedFileIds();
        $actionName = $actionRequested->getAction()->getActionName();

        if (empty($hostedFiles) || count($hostedFiles) !== 1) {
            var_dump($hostedFiles);
            throw new \RuntimeException('VirusScannerService, File not found');
        }

        $hostedFile = $this->hostedFileRepository->findOneBy(['id' => $hostedFiles[0]['fileId']]);
        if (!$hostedFile) {
            throw new \RuntimeException('VirusScannerService, file is deleted');
        }

        if (!$actionName) {
            throw new \RuntimeException('VirusScannerService, ActionName required');
        }

        if (!is_dir($this->actionsResultsDirectory) && !mkdir($concurrentDirectory = $this->actionsResultsDirectory) && !is_dir($concurrentDirectory)) {
            throw new \RuntimeException(sprintf('Directory "%s" was not created', $concurrentDirectory));
        }

        $actionDestinationParent = $this->actionsResultsDirectory.DIRECTORY_SEPARATOR.$actionName;
        if (!is_dir($actionDestinationParent) && !mkdir($actionDestinationParent) && !is_dir($actionDestinationParent)) {
            throw new \RuntimeException(sprintf('Directory "%s" was not created', $actionDestinationParent));
        }

        $actionDestination = $actionDestinationParent.DIRECTORY_SEPARATOR.$actionRequested->getId();
        if (!is_dir($actionDestination) && !mkdir($actionDestination) && !is_dir($actionDestination)) {
            throw new \RuntimeException(sprintf('Directory "%s" was not created', $actionDestination));
        }

        $actionResultFileName = $hostedFile->getName().'.log';
        $fullDestinationPath = $actionDestination.DIRECTORY_SEPARATOR.$hostedFile->getName().'.log';

        $commandParameters = '-r --move='.$this->actionsResultsDirectory.' '.$this->hostingDirectory.$hostedFile->getName().' -l '.$fullDestinationPath;
        $actionRequested->setStartTime(new \DateTime('now', new \DateTimeZone('Europe/Paris')));
        $actionRequested->setActionParameters($commandParameters);
        $fullCommandToRun = $actionRequested->getAction()->getCommandToRun().' '.$commandParameters;

        if ($this->kernelEnvironment === 'dev') {
            $this->simulateScan($fullDestinationPath);
            $fullCommandToRun = 'ls';
        }

        exec($fullCommandToRun);

        if (!file_exists($fullDestinationPath)) {
            throw new \RuntimeException(sprintf('Please check AntiVirus installation : '.$fullDestinationPath.' command : '.$fullCommandToRun));
        }

        $actionRequested->setEndTime(new \DateTime('now', new \DateTimeZone('Europe/Paris')));
        $actionRequested->setAccomplished(true);
        $actionResults = $actionRequested->getActionResults();
        $actionResults[] = $actionResultFileName;
        $actionRequested->setActionResults($actionResults);
        $scanResult = file_get_contents($fullDestinationPath);
        $scanResult = str_replace([$actionDestinationParent, $this->actionsResultsDirectory, $this->hostingDirectory, $hostedFile->getName()],
            ['', '/actionsResultsDirectory/', '/', $hostedFile->getClientName()], $scanResult);
        $scanResult = '[REF : '.$hostedFile->getName()." ] \n".$scanResult;

        $isInfected = false;
        if (str_contains($scanResult, 'Infected files: 1')) {
            $hostedFile->setInfected(true);
            $isInfected = true;
        }

        $hostedFile->setScanResult($scanResult);
        $hostedFile->setScaned(true);
        $em = $this->doctrine->getManager();
        $em->persist($hostedFile);
        $em->flush();

        $this->notifyUser($scanResult, $isInfected, $actionRequested->getUser(), $hostedFile);
    }

    private function notifyUser($scanResult, $isInfected, $user, $hostedFile): void
    {
        $subject = $isInfected === true ? 'Your file is infected' : 'Your file is safe';
        if ($user->isSendEmailAfterEachAction()) {
            $email = (new TemplatedEmail())
                ->from($this->webSiteEmailAddress)
                ->to($user->getEmail())
                // ->cc('cc@example.com')
                // ->bcc('bcc@example.com')
                // ->replyTo('fabien@example.com')
                // ->priority(Email::PRIORITY_HIGH)
                ->subject($subject)
                ->htmlTemplate('app/mails/scan-result.html.twig')
                ->context([
                    'scanResult' => $scanResult,
                    'fileDescription' => $hostedFile->getDescription(),
                    'lang' => 'en',
                    'isInfected' => $isInfected
                ]);

            $this->mailer->send($email);
        }
    }

    public function simulateScan(string $logPath): void
    {
        sleep(20);
        file_put_contents($logPath, '
-------------------------------------------------------------------------------


----------- SCAN SUMMARY -----------
Known viruses: 8641488
Engine version: 0.103.7
Scanned directories: 0
Scanned files: 1
Infected files: 0
Data scanned: 24.87 MB
Data read: 14.12 MB (ratio 1.76:1)
Time: 41.191 sec (0 m 41 s)
Start Date: 2022:10:29 17:49:09
End Date:   2022:10:29 17:49:50');
    }
}
