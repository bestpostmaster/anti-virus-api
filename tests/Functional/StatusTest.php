<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class StatusTest extends WebTestCase
{
    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function testStatusRouteIsOk(): void
    {
        $this->client->request(
            'GET',
            '/status'
        );

        $response = $this->client->getResponse();

        static::assertEquals(200, $response->getStatusCode());
        static::assertEquals('ok', $response->getContent());
    }
}