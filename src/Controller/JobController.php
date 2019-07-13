<?php

namespace App\Controller;

use App\Entity\Job;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class JobController extends AbstractController
{
    /**
     * @Route("/job", name="job", methods={"GET"})
     */
    public function getJobs()
    {
        return $this->json([
            "message" => "welcome to my controller",
            "path" => "src/Controller/JobController.php"
        ]);
    }

    /**
     * @Route("/job", name="job", methods={"POST"})
     */
    public function createPost()
    {
        return $this->json([
            "message" => "post function",
            "path" => "src/Controller/JobController.php"
        ]);
    }

    /**
     * @Route("/update", name="update", methods={"PUT"})
     */
    public function updateJob()
    {
        return $this->json([
            "message" => "update function",
            "path" => "src/Controller/JobController.php"
        ]);
    }

    /**
     * @param Job $job
     * @Route("/job/{id}", name="update", methods={"PATCH"})
     */
    public function updateJobTitle(Job $job)
    {

    }

}
