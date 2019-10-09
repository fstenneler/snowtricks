<?php

namespace App\Tests;

use PHPUnit\Framework\TestCase;
use App\Controller\TrickController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TrickControllerTest extends WebTestCase
{

    private $client = null;

    public function setUp()
    {
        $this->client = static::createClient();
    }
    
    public function testIndexResponse()
    {
        $this->client->request('GET', '/');
        $this->assertEquals(
            Response::HTTP_OK,
            $this->client->getResponse()->getStatusCode()
        );
        
    }
    
    public function testTricksResponse()
    {
        $this->client->request('GET', '/tricks');
        $this->assertEquals(
            Response::HTTP_OK,
            $this->client->getResponse()->getStatusCode()
        );
        
    }
    
    public function testLoadResultsResponse()
    {
        $this->client->request('GET', '/load-results/0/1/creationDate-DESC');
        $this->assertEquals(
            Response::HTTP_OK,
            $this->client->getResponse()->getStatusCode()
        );
        
    }


}
