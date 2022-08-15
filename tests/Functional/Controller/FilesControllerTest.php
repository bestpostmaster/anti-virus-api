<?php

namespace App\Tests\Functional\Controller;

use App\Repository\HostedFileRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FilesControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private string $hostingDirectory;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->hostingDirectory = static::getContainer()->getParameter('kernel.project_dir').'/public/up/';
    }

    /**
     * @throws \JsonException
     */
    public function testUploadFile(): void
    {
        $userRepository = static::getContainer()->get(UserRepository::class);

        $admin = $userRepository->findOneBy(['login' =>'admin']);

        $this->client->loginUser($admin);

        $fixturesPathFromRoot = dirname(__DIR__, 3).'/src/DataFixtures/Files/';
        $fileName = scandir($fixturesPathFromRoot, 1)[0];

        $fullFilePath = $fixturesPathFromRoot.$fileName;

        $uploadedFile = new UploadedFile(
            $fullFilePath,
            'test.jpg'
        );

        // Attention, the fixtures file is deleted, it must be secured before uploading
        copy($fullFilePath, $fullFilePath.'.tmp');

        $this->client->request('POST', '/api/files/upload', ['description' => 'My awesome image!'],
            [
                'file' => $uploadedFile
            ]
        );

        rename($fullFilePath.'.tmp', $fullFilePath);

        $responseData = json_decode(($this->client->getResponse())->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertResponseIsSuccessful();
        self::assertSame($responseData['description'], 'My awesome image!');
        self::assertTrue(file_exists(            $this->hostingDirectory.$responseData['name']));
        self::assertTrue($responseData['size'] === round(filesize($this->hostingDirectory.$responseData['name'])/1000000, 4));
    }
}