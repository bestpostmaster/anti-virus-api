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

    public function __construct(ManagerRegistry $doctrine, string $hostingDirectory, string $quarantineDirectory, string $projectDirectory)
    {
        $this->doctrine = $doctrine;
        $this->hostingDirectory = $hostingDirectory;
        $this->quarantineDirectory = $quarantineDirectory;
        $this->projectDirectory = $projectDirectory;
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
        exec($actionRequested->getAction()->getCommandToRun().' '.$commandParameters);

        if (!file_exists($logPath)) {
            throw new \RuntimeException(sprintf('Please check AntiVirus installation : '.$logPath.' command : '.'clamscan -r --move='.$this->quarantineDirectory.' '.$this->hostingDirectory.$hostedFile->getName().' -l '.$logPath));
        }

        $actionRequested->setEndTime(new \DateTime('now', new \DateTimeZone('Europe/Paris')));
        $actionRequested->setAccomplished(true);
        $scanResult = file_get_contents($this->projectDirectory.$varLog.$hostedFile->getName().'-ScanResult.log');
        $scanResult = str_replace($this->projectDirectory.$varLog, '', $scanResult);
        $scanResult = str_replace($this->quarantineDirectory, '/quarantine/', $scanResult);
        $scanResult = str_replace($this->hostingDirectory, '/', $scanResult);
        $scanResult = str_replace($hostedFile->getName(), $hostedFile->getClientName(), $scanResult);
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
}
