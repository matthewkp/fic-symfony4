<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HomepageTest extends WebTestCase
{
    public function testHomepage()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/en');

        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("Welcome")')->count()
        );
    }
}
