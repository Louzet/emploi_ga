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
use Symfony\Component\Serializer\SerializerInterface;

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
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * JobController constructor.
     * @param JobRepository $jobRepository
     * @param EntityManagerInterface $entityManager
     * @param RequestStack $request
     * @param SerializerInterface $serializer
     */
    public function __construct(JobRepository $jobRepository, EntityManagerInterface $entityManager, RequestStack $request, SerializerInterface $serializer)
    {
        $this->jobRepository = $jobRepository;
        $this->entityManager = $entityManager;
        $this->request = $request;
        $this->serializer = $serializer;
    }

    /**
     * @Rest\Route("/jobs", name="jobs_list", methods={"GET"}, options={"method_prefix"=false, "expose"=true })
     * @return JsonResponse
     */
    public function getJobsList()
    {
        $jobs = $this->jobRepository->findAll();

        $data = $this->serializer->serialize($jobs, 'json');

        return new JsonResponse($data, JsonResponse::HTTP_OK, [], true);
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

            $message = ['code' => 201, 'message' => "job $title was successfully created"];
            $message = $this->serializer->serialize($message, 'json');

            return new JsonResponse($message, Response::HTTP_CREATED, [], true);
        }

        $message = ['code' => 400, 'message' => 'Invalid request'];
        $message = $this->serializer->serialize($message, 'json');

        return new JsonResponse($message, Response::HTTP_BAD_REQUEST, [], true);
    }

    /**
     * @Rest\Route("/jobs/{id}", name="jobs_view", methods={"GET"}, options={"method_prefix"=false, "expose"=true })
     * @return JsonResponse
     */
    public function getOneJob()
    {
        $job_id = $this->request->getMasterRequest()->attributes->get('id');
        $job = $this->jobRepository->find($job_id);

        if (!$job){
            $message = ['code' => 404, 'message' => 'resource not found'];
            $message = $this->serializer->serialize($message, 'json');
            return new JsonResponse($message, Response::HTTP_NOT_FOUND, [], true);
        }

        $data = $this->serializer->serialize($job, 'json');

        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }

}
