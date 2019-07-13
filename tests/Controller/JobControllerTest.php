<?php


namespace App\Tests\Controller;


use App\Repository\JobRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;

/**
 * @group functional
 *
 * Class JobControllerTest
 * @package App\Tests\Controller
 */
class JobControllerTest extends WebTestCase
{
    private $jobRepository;

    private $entityManager;

    private $request;

    public function settUp()
    {
        parent::setUp();

        $this->jobRepository = $this->getMockBuilder(JobRepository::class)->getMock();
        $this->entityManager = $this->getMockBuilder(EntityManagerInterface::class)->getMock();
        $this->request       = $this->getMockBuilder(RequestStack::class)->getMock();
    }

    /**
     * @dataProvider getInvalidUrlForSmokeTest
     * @param $method
     * @param $url
     */
    public function testGetJobListReturnErrorWithInvalidUrl($method, $url)
    {
        $client = static::createClient();
        $client->request($method, $url);
        $this->assertEquals(Response::HTTP_NOT_FOUND, $client->getResponse()->getStatusCode());

        $response = $client->getResponse();
        $content = json_decode($response->getContent(), true);

        $this->assertIsArray($content);

        $this->assertJson('{"code":404, "message":"No route found for \"GET \/api\/jobss\""}',
            $response->getContent());
    }



    /**
     * @dataProvider postUrlForSmockTest
     * @param string $method
     * @param string $url
     */
    public function testCreatePostWithErrors(string $method, string $url)
    {
        $client = static::createClient();

        $data = [
            'title'        => 'directeur adjoint',
            'description'  => '',
            'priceMinimum' => 1000000,
            'priceMaximum' => 1500000
        ];

        $client->request($method, $url, $data);
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $client->getResponse()->getStatusCode());
    }

    // TODO don't test the correct behaviour, we don't need to create one job everytime
    /*public function testCreatePostWithoutErrors($method, $url)
    {
        $client = static::createClient();

        $data = [
            'title'        => 'directeur adjoint',
            'description'  => 'vous aurez en charge l\'administration de notre start-up situé n\'importe où',
            'priceMinimum' => 1000000,
            'priceMaximum' => 1500000
        ];

        $client->request($method, $url, $data);
        $this->assertEquals(Response::HTTP_CREATED, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->isRedirect());
    }*/

    ## dataProviders ##

    public function getInvalidUrlForSmokeTest()
    {
        yield ['GET', '/api/jobsss'];
    }

    public function getInvalidJobResource()
    {
        yield ['GET', '/api/jobs/66699987618993'];
    }

    public function getUrlForSmokeTest()
    {
        yield ['GET', '/api/jobs'];
        yield ['GET', '/api/jobs/1'];
    }

    public function postUrlForSmockTest()
    {
        yield ['POST', '/api/jobs'];
    }
}