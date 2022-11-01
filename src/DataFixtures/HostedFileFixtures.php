<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\ActionRequested;
use App\Entity\HostedFile;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class HostedFileFixtures extends Fixture implements DependentFixtureInterface
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

    public function load(ObjectManager $manager): void
    {
        $users = $manager->getRepository(User::class)->findAll();
        $files = $this->getFilesFixturesFromUsers($users);
        $currentTime = new \DateTime('2024-07-05T06:00:00Z', new \DateTimeZone('Europe/Paris'));

        foreach ($files as $item) {
            $file = new HostedFile();
            $file->setName($item['name']);
            $file->setClientName($item['clientName']);
            $file->setUploadDate($item['uploadDate']);
            $file->setExpirationDate($item['expirationDate']);
            $file->setUser($item['user']);
            $file->setSize((int) $item['size']);
            $file->setScaned($item['scaned']);
            $file->setDescription($item['description']);
            $file->setDownloadCounter($item['downloadCounter']);
            $file->setUrl($item['url']);
            $file->setUploadLocalisation($item['uploadLocalisation']);
            $file->setCopyrightIssue($item['copyrightIssue']);
            $file->setConversionsAvailable($item['conversionsAvailable']);
            $file->setVirtualDirectory($item['virtualDirectory']);

            $actionRequested = new ActionRequested();
            $actionRequested->setDateOfDemand($currentTime);
            $varLog = DIRECTORY_SEPARATOR.'var'.DIRECTORY_SEPARATOR.'log'.DIRECTORY_SEPARATOR;
            $logPath = $this->projectDirectory.$varLog.$file->getName().'-ScanResult.log';
            $actionRequested->setActionParameters('-r --move='.$this->actionsResultsDirectory.' '.$this->hostingDirectory.$file->getName().' -l '.$logPath);
            $actionRequested->setHostedFileIds([]);
            $actionRequested->setActionResults([]);

            $manager->persist($file);
            $manager->flush($file);
        }
    }

    private function getFilesFixturesFromUsers(array $users): array
    {
        $names = $this->getFilesNames();

        $this->copyFilesFixtures();

        $files = [
            [
                'name' => $names[0],
                'clientName' => 'test-cli-name'.$users[0]->getId().'.jpg',
                'uploadDate' => new \DateTime('now'),
                'expirationDate' => new \DateTime('2024-07-05T06:00:00Z', new \DateTimeZone('Europe/Paris')),
                'user' => $users[0],
                'size' => 99191951951,
                'scaned' => false,
                'description' => 'ééé ààà desc',
                'downloadCounter' => 0,
                'url' => 'file-1',
                'uploadLocalisation' => '127.0.0.1',
                'copyrightIssue' => false,
                'conversionsAvailable' => 'jpg,png',
                'virtualDirectory' => 'test-dir'.$users[0]->getId(),
            ],
            [
                'name' => $names[1],
                'clientName' => 'test'.$users[0]->getId().'.jpg',
                'uploadDate' => new \DateTime('now'),
                'expirationDate' => new \DateTime('2024-07-05T06:00:00Z', new \DateTimeZone('Europe/Paris')),
                'user' => $users[0],
                'size' => 6516165161,
                'scaned' => false,
                'description' => 'ééé bbb desc',
                'downloadCounter' => 0,
                'url' => 'file-2',
                'uploadLocalisation' => '127.0.0.1',
                'copyrightIssue' => false,
                'conversionsAvailable' => 'jpg,png',
                'virtualDirectory' => 'test-dir'.$users[0]->getId(),
            ],
            [
                'name' => $names[2],
                'clientName' => 'test-cli-name'.$users[1]->getId().'.jpg',
                'uploadDate' => new \DateTime('now'),
                'expirationDate' => new \DateTime('2024-07-05T06:00:00Z', new \DateTimeZone('Europe/Paris')),
                'user' => $users[1],
                'size' => 99191951951,
                'scaned' => false,
                'description' => 'ééé ààà desc',
                'downloadCounter' => 0,
                'url' => 'file-3',
                'uploadLocalisation' => '127.0.0.1',
                'copyrightIssue' => false,
                'conversionsAvailable' => 'jpg,png',
                'virtualDirectory' => 'test-dir'.$users[1]->getId(),
            ],
            [
                'name' => $names[3],
                'clientName' => 'test'.$users[1]->getId().'.jpg',
                'uploadDate' => new \DateTime('now'),
                'expirationDate' => new \DateTime('2024-07-05T06:00:00Z', new \DateTimeZone('Europe/Paris')),
                'user' => $users[1],
                'size' => 6516165161,
                'scaned' => false,
                'description' => 'ééé bbb desc',
                'downloadCounter' => 0,
                'url' => 'file-4',
                'uploadLocalisation' => '127.0.0.1',
                'copyrightIssue' => false,
                'conversionsAvailable' => 'jpg,png',
                'virtualDirectory' => 'test-dir'.$users[1]->getId(),
            ],
        ];

        return $files;
    }

    public function getFilesNames(): array
    {
        return [
            '5fg4h61h6dfh65f6fgh6fh6fgh46fg6d5f.jpg',
            '54hd6f5h6dfg5h4d6fgh6fdg5h65fz6rd6f5gh.png',
            'df5g4h6df5g4h6f5g4h6fdgh6f5g1h6fg51h.mp4',
            'f65g4hdfg1hfgh6f5g1h5fgh9fhgff1ghf65.pdf',
        ];
    }

    public function copyFilesFixtures(): void
    {
        if (!is_dir($this->hostingDirectory)) {
            if (!mkdir($concurrentDirectory = $this->hostingDirectory) && !is_dir($concurrentDirectory)) {
                throw new \RuntimeException(sprintf('Directory "%s" was not created', $concurrentDirectory));
            }
        }

        $names = $this->getFilesNames();

        foreach ($names as $name) {
            copy($this->projectDirectory.'/src/DataFixtures/Files/'.$name, $this->hostingDirectory.$name);
        }
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
        ];
    }
}
