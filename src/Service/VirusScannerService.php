<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\ActionRequested;
use App\Repository\HostedFileRepository;
use Doctrine\Persistence\ManagerRegistry;

class VirusScannerService
{
    private ManagerRegistry $doctrine;
    private string $hostingDirectory;
    private string $actionsResultsDirectory;
    private string $projectDirectory;
    private string $kernelEnvironment;
    private HostedFileRepository $hostedFileRepository;

    public function __construct(ManagerRegistry $doctrine, string $hostingDirectory, string $actionsResultsDirectory, string $projectDirectory, string $kernelEnvironment, HostedFileRepository $hostedFileRepository)
    {
        $this->doctrine = $doctrine;
        $this->hostingDirectory = $hostingDirectory;
        $this->actionsResultsDirectory = $actionsResultsDirectory;
        $this->projectDirectory = $projectDirectory;
        $this->kernelEnvironment = $kernelEnvironment;
        $this->hostedFileRepository = $hostedFileRepository;
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

        $actionDestination = $this->actionsResultsDirectory.DIRECTORY_SEPARATOR.$actionName;
        if (!is_dir($actionDestination) && !mkdir($actionDestination) && !is_dir($actionDestination)) {
            throw new \RuntimeException(sprintf('Directory "%s" was not created', $actionDestination));
        }

        $logPath = $actionDestination.DIRECTORY_SEPARATOR.$hostedFile->getName().'.log';
        $commandParameters = '-r --move='.$this->actionsResultsDirectory.' '.$this->hostingDirectory.$hostedFile->getName().' -l '.$logPath;
        $actionRequested->setStartTime(new \DateTime('now', new \DateTimeZone('Europe/Paris')));
        $actionRequested->setActionParameters($commandParameters);
        $fullCommandToRun = $actionRequested->getAction()->getCommandToRun().' '.$commandParameters;

        if ($this->kernelEnvironment === 'dev') {
            $this->simulateScan($logPath);
            $fullCommandToRun = 'ls';
        }

        exec($fullCommandToRun);

        if (!file_exists($logPath)) {
            throw new \RuntimeException(sprintf('Please check AntiVirus installation : '.$logPath.' command : '.'clamscan -r --move='.$this->actionsResultsDirectory.' '.$this->hostingDirectory.$hostedFile->getName().' -l '.$logPath));
        }

        $actionRequested->setEndTime(new \DateTime('now', new \DateTimeZone('Europe/Paris')));
        $actionRequested->setAccomplished(true);
        $actionResults = $actionRequested->getActionResults();
        $actionResults[] = $logPath;
        $actionRequested->setActionResults($actionResults);
        $scanResult = file_get_contents($logPath);
        $scanResult = str_replace([$actionDestination, $this->actionsResultsDirectory, $this->hostingDirectory, $hostedFile->getName()],
            ['', '/actionsResultsDirectory/', '/', $hostedFile->getClientName()], $scanResult);
        $scanResult = '[REF : '.$hostedFile->getName()." ] \n".$scanResult;

        if (str_contains($scanResult, 'Infected files: 1')) {
            $hostedFile->setInfected(true);
        }

        $hostedFile->setScanResult($scanResult);
        $hostedFile->setScaned(true);
        $em = $this->doctrine->getManager();
        $em->persist($hostedFile);
        $em->flush();
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
