<?php

namespace tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BooksControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/books');
        
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('Books', $crawler->filter('.container h1')->text());
    }
}
