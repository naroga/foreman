<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TesteControllerTest extends WebTestCase
{
    public function testTeste()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/teste2');
    }

}
