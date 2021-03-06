<?php

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RaceMainControllerTest extends WebTestCase
{
    public function testHomePageIsAvailable()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        // Assertions
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertSelectorTextContains('html h1', 'Horse Race Simulator');
        $this->assertEquals(
            1,
            $crawler->selectLink('Create Race')->count()
        );

        $this->assertEquals(
            1,
            $crawler->selectLink('Progress')->count()
        );
    }
}
