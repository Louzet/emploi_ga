<?php

namespace App\Tests\Entity;

use App\Entity\Job;
use App\Exceptions\JobException;
use Symfony\Bundle\FrameworkBundle\Tests\TestCase;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Validator\Exception\InvalidArgumentException;

class JobTest extends TestCase
{
    public function testConstruct()
    {
        $job = new Job(
            "developpeur",
            "créer des programmes informatiques",
            500000,
            1000000
        );
        $this->assertFalse($job->getIsPublished());
        $this->assertEquals("developpeur", $job->getTitle());
        $this->assertEquals("créer des programmes informatiques", $job->getDescription());
        $this->assertEquals(500000, $job->getPriceMinimum());
        $this->assertEquals(1000000, $job->getPriceMaximum());
        $this->assertInstanceOf(\DateTimeInterface::class, $job->getCreatedAt());
    }

    public function testJobIsPublished()
    {
        $job = new Job(
            "Préparateur de commandes",
            "Préparer les commandes des clients (sur la base de la commande vocale, 
                aller dans le picking pour prendre les colis et les mettre sur palettes).
                Participer à la manutention avec l’utilisation d’un transpalette électrique, 
                au contrôle des colis et palettes et au nettoyage de l’entrepôt.",
            225000,
            350000

        );
        $this->assertFalse($job->getIsPublished());
        $this->assertNull($job->getPublishedAt());
        $job->publish();
        $this->assertTrue($job->getIsPublished());
        $this->assertInstanceOf(\DateTimeInterface::class, $job->getPublishedAt());
    }

    public function testDontAllowEmptyTitleAndDescription()
    {
        $this->expectException(BadRequestHttpException::class);
        $this->expectExceptionMessage("some fields are required");
        $this->expectExceptionCode(400);
        new Job("", "", 0, 0);
    }

    public function testCreatedAgoReturnHumansFormat()
    {
        $job = new Job(
            "animateur",
            "En liaison avec le producteur (qu'il peut aussi être lui-même), 
            le chroniqueur TV/Radio décide de la ligne éditoriale et du fil de l'émission qu'il va devoir animer. 
            Seul ou en équipe, il réalise ensuite un important travail en amont de l'émission :
            Il choisit les sujets à traiter.
            Il définit le style et le rythme à adopter.
            Il s'imprègne des thèmes en se documentant.",
            850000,
            1100000
        );
        $this->assertIsString($job->getCreatedAtAgo());
    }

    public function testJobCanReturnShortDescription()
    {
        $job = new Job("blablabla", "blabla", 1000, 2000);
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