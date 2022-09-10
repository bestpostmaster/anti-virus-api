<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\HostedFile;
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

    public function scan(HostedFile $hostedFile): void
    {
        if (!is_dir($this->quarantineDirectory)) {
            mkdir($this->quarantineDirectory);
        }

        exec('clamscan -r --move='.$this->quarantineDirectory.' '.$this->hostingDirectory.$hostedFile->getName().' -l '.$this->projectDirectory.'/var/log/'.$hostedFile->getName().'-ScanResult.log');
        $scanResult = file_get_contents($this->projectDirectory.'/var/log/'.$hostedFile->getName().'-ScanResult.log');
        $scanResult = str_replace($this->projectDirectory.'/var/log/', '', $scanResult);
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
