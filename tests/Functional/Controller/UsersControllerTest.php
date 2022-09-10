<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UsersControllerTest extends WebTestCase
{
    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function testNonAdminCanNotAddUser(): void
    {
        $this->client->request(
            'GET',
            '/api/admin/users/add'
        );

        $response = $this->client->getResponse();

        static::assertEquals(401, $response->getStatusCode());
    }

    public function testAdminCanAddUser(): void
    {
        $userRepository = static::getContainer()->get(UserRepository::class);

        $admin = $userRepository->findOneBy(['login' => 'admin']);

        $this->client->loginUser($admin);

        $login = uniqid((string) mt_rand(), true);

        $this->client->jsonRequest('POST', '/api/admin/users/add', [
            'username' => $login,
            'password' => '5g4h6fghf6ghfh65fgh46',
            'roles' => [
                'ROLE_USER',
            ],
        ]);

        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        self::assertResponseIsSuccessful();
        self::assertSame($responseData['login'], $login);
    }

    public function testUserCanNotAddUser(): void
    {
        $userRepository = static::getContainer()->get(UserRepository::class);

        $admin = $userRepository->findOneBy(['login' => 'user']);

        $this->client->loginUser($admin);

        $this->client->jsonRequest('POST', '/api/admin/users/add', [
            'username' => uniqid((string) mt_rand(), true),
            'password' => '5g4h6fghf6ghfh65fgh46',
            'roles' => [
                'ROLE_USER',
            ],
        ]);

        self::assertResponseStatusCodeSame(403);
    }
}
