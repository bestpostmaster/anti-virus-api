<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\ActionRequested;
use Doctrine\Persistence\ManagerRegistry;

class VirusScannerService
{
    private ManagerRegistry $doctrine;
    private string $hostingDirectory;
    private string $quarantineDirectory;
    private string $projectDirectory;
    private string $kernelEnvironment;

    public function __construct(ManagerRegistry $doctrine, string $hostingDirectory, string $quarantineDirectory, string $projectDirectory, string $kernelEnvironment)
    {
        $this->doctrine = $doctrine;
        $this->hostingDirectory = $hostingDirectory;
        $this->quarantineDirectory = $quarantineDirectory;
        $this->projectDirectory = $projectDirectory;
        $this->kernelEnvironment = $kernelEnvironment;
    }

    public function runCommand(ActionRequested $actionRequested): void
    {
        $hostedFile = $actionRequested->getHostedFile();

        if (!$hostedFile) {
            throw new \RuntimeException('VirusScannerService, File not fount');
        }

        if (!is_dir($this->quarantineDirectory) && !mkdir($concurrentDirectory = $this->quarantineDirectory) && !is_dir($concurrentDirectory)) {
            throw new \RuntimeException(sprintf('Directory "%s" was not created', $concurrentDirectory));
        }

        $varLog = DIRECTORY_SEPARATOR.'var'.DIRECTORY_SEPARATOR.'log'.DIRECTORY_SEPARATOR;
        $logPath = $this->projectDirectory.$varLog.$hostedFile->getName().'-ScanResult.log';
        $commandParameters = '-r --move='.$this->quarantineDirectory.' '.$this->hostingDirectory.$hostedFile->getName().' -l '.$logPath;
        $actionRequested->setStartTime(new \DateTime('now', new \DateTimeZone('Europe/Paris')));
        $actionRequested->setActionParameters($commandParameters);
        $fullCommandToRun = $actionRequested->getAction()->getCommandToRun().' '.$commandParameters;

        if ($this->kernelEnvironment === 'dev') {
            $fullCommandToRun = 'php bin/console app:simulate-scan -log '.$fullCommandToRun;
        }

        exec($fullCommandToRun);

        if (!file_exists($logPath)) {
            throw new \RuntimeException(sprintf('Please check AntiVirus installation : '.$logPath.' command : '.'clamscan -r --move='.$this->quarantineDirectory.' '.$this->hostingDirectory.$hostedFile->getName().' -l '.$logPath));
        }

        $actionRequested->setEndTime(new \DateTime('now', new \DateTimeZone('Europe/Paris')));
        $actionRequested->setAccomplished(true);
        $scanResult = file_get_contents($logPath);
        $scanResult = str_replace([$this->projectDirectory.$varLog, $this->quarantineDirectory, $this->hostingDirectory, $hostedFile->getName()],
            ['', '/quarantine/', '/', $hostedFile->getClientName()], $scanResult);
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

    private function simulateAntivirusScan()
    {
    }
}
