<?php

namespace App\Tests\Services;

use App\Entity\Job;
use App\Services\JobManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper;
use Symfony\Bundle\FrameworkBundle\Tests\TestCase;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class JobManagerTest extends TestCase
{

    public function testCreateNewJob()
    {
        $stubJob = $this->createMock(JobManager::class);
        $stubJob
            ->method('createNewJob')
            ->willReturn(Job::class)
        ;

        $this->assertSame(
            Job::class,
            $stubJob->createNewJob(
                'title',
                'description',
                1000,
                2000
            )
        );
    }

    public function testFailedCreationNewJob()
    {
        $jobManager = new JobManager();
        $this->expectException(BadRequestHttpException::class);
        $this->expectExceptionMessage("some fields are required");
        $this->expectExceptionCode(400);
        $jobManager->createNewJob('', 'blablabla', 100, 200);
    }
}