<?php

namespace Tests\Functional\Api;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\JsonResponse;

class DefaultControllerTest extends WebTestCase
{
    public function testDefaultEndpoint(): void
    {
        $client = static::createClient();

        $client->request(
            'GET',
            '/',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json']
        );

        $response = $client->getResponse();
        $content = json_decode($response->getContent(), true);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals(['ping' => 'pong'], $content);
    }
}
