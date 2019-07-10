<?php

namespace App\Controller;

use App\Entity\Job;
use App\Repository\JobRepository;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class JobController
 * @package App\Controller
 */
class JobController extends AbstractFOSRestController
{

    /**
     * @var JobRepository
     */
    private $jobRepository;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var Request
     */
    private $request;

    /**
     * JobController constructor.
     * @param JobRepository $jobRepository
     * @param EntityManagerInterface $entityManager
     * @param RequestStack $request
     */
    public function __construct(JobRepository $jobRepository, EntityManagerInterface $entityManager, RequestStack $request)
    {
        $this->jobRepository = $jobRepository;
        $this->entityManager = $entityManager;
        $this->request = $request;
    }

    /**
     * @Rest\Route("/jobs", name="jobs_list", methods={"GET"}, options={"method_prefix"=false, "expose"=true })
     * @return JsonResponse
     */
    public function getJobsList()
    {
        $data = $this->jobRepository->findAll();

        return $this->json($data, JsonResponse::HTTP_OK);
    }

    /**
     * @Rest\Route("/jobs", name="jobs_create", methods={"POST"}, options={"expose"=true })
     * @return JsonResponse
     */
    public function createJob()
    {
        $title        = $this->request->getMasterRequest()->get('title');
        $description  = $this->request->getMasterRequest()->get('description');
        $priceMinimum = $this->request->getMasterRequest()->get('priceMinimum');
        $priceMaximum = $this->request->getMasterRequest()->get('priceMaximum');

        if (!is_null($title) && !is_null($description) && !is_null($priceMinimum) && !is_null($priceMaximum)){
            $job = new Job($title, $description, $priceMinimum, $priceMaximum);

            $this->entityManager->persist($job);
            $this->entityManager->flush();

            return $this->json(['code' => 201, 'message' => "job $title was successfully created"], Response::HTTP_CREATED);
        }

        return $this->json(['code' => 400, 'message' => 'Invalid request'], Response::HTTP_BAD_REQUEST);
    }

    /**
     * @Rest\Route("/jobs/{id}", name="jobs_view", methods={"GET"}, options={"method_prefix"=false, "expose"=true })
     * @return JsonResponse
     */
    public function getOneJob()
    {
        $job_id = $this->request->getMasterRequest()->attributes->get('id');
        $data = $this->jobRepository->find($job_id);

        if (!$data){
            return $this->json(['code' => 404, 'message' => 'resource not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($data, Response::HTTP_OK);
    }

}
