<?php

namespace App\Tests\Entity;

use App\Entity\Job;
use App\Exceptions\JobException;
use Symfony\Bundle\FrameworkBundle\Tests\TestCase;
use Symfony\Component\Validator\Exception\InvalidArgumentException;

class JobTest extends TestCase
{
    public function testConstruct()
    {
        $job = new Job("developer");
        $this->assertFalse($job->getIsPublished());
        $this->assertEquals("developer", $job->getTitle());
        $this->assertInstanceOf(\DateTimeInterface::class, $job->getCreatedAt());
    }

    public function testJobIsPublished()
    {
        $job = new Job("Préparateur de commandes");
        $this->assertFalse($job->getIsPublished());
        $this->assertNull($job->getPublishedAt());
        $job->publish();
        $this->assertTrue($job->getIsPublished());
        $this->assertInstanceOf(\DateTimeInterface::class, $job->getPublishedAt());
    }

    public function testDontAllowEmptyTitleAndDescription()
    {
        $this->expectException(JobException::class);
        $this->expectExceptionCode(400);
        new Job("");
    }

    public function testCreatedAgoReturnHumansFormat()
    {
        $job = new Job("animator");
        $this->assertIsString($job->getCreatedAtAgo());
    }

    public function testJobCanReturnShortDescription()
    {
        $job = new Job("blablabla");
        $job->setTextDescription("
            Votre défi : 
            offrir à chaque client un moment de plaisir par un accueil et un sens du service forts. 
            Sourire, écoute et sens du service sont vos plus grands atouts puisqu'ils 
            vous permettent de créer un lien privilégié avec le voyageur, 
            de comprendre ses attentes et de satisfaire ses envies."
        );
        $this->assertGreaterThan(strlen($job->getShortDescription()), strlen($job->getDescription()));
    }
}