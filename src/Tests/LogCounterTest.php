<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LogCounterTest extends WebTestCase
{
    public function testSomething(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/count',["serviceNames[]"=>"USER-SERVICE"]);

        $this->assertResponseIsSuccessful();

        // $this->assertSelectorTextContains('h1', 'Hello World');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $responseData = json_decode($client->getResponse()->getContent(), true);
    }
}
