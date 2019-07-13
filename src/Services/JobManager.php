<?php

namespace App\Services;

use App\Entity\Job;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class JobManager
{

    public function createNewJob(string $title, string $description, int $priceMinimum, int $priceMaximum)
    {
        try {
            $job = new Job($title, $description, $priceMinimum, $priceMaximum);
            return $job;
        } catch (\Exception $e) {
            throw new BadRequestHttpException("some fields are required", null, 400);
        }
    }

}