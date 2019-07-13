<?php

namespace App\Controller;

use App\Entity\Job;
use App\Repository\JobRepository;
use App\Services\JobManager;
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
     * @var JobManager
     */
    private $jobManager;

    /**
     * JobController constructor.
     * @param JobRepository $jobRepository
     * @param EntityManagerInterface $entityManager
     * @param RequestStack $request
     * @param SerializerInterface $serializer
     * @param JobManager $jobManager
     */
    public function __construct(JobRepository $jobRepository, EntityManagerInterface $entityManager, RequestStack $request, SerializerInterface $serializer, JobManager $jobManager)
    {
        $this->jobRepository = $jobRepository;
        $this->entityManager = $entityManager;
        $this->request = $request;
        $this->serializer = $serializer;
        $this->jobManager = $jobManager;
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
            $this->jobManager->createNewJob($title, $description, $priceMinimum, $priceMaximum);

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

    /**
     * @Rest\Route("/jobs/{id}", name="jobs_delete", methods={"DELETE"}, options={"method_prefix"=false, "expose"=true })
     * @return JsonResponse
     */
    public function deleteJob()
    {
        $job_id = $this->request->getMasterRequest()->attributes->get('id');
        $job = $this->jobRepository->find($job_id);

        if (!$job){
            $message = ['code' => 404, 'message' => 'resource not found'];
            $message = $this->serializer->serialize($message, 'json');
            return new JsonResponse($message, Response::HTTP_NOT_FOUND, [], true);
        }

        $this->entityManager->remove($job);
        $this->entityManager->flush();

        $message = ['code' => 204, 'message' => 'resource deleted successfully'];
        $message = $this->serializer->serialize($message, 'json');

        return new JsonResponse($message, Response::HTTP_OK, [], true);
    }

    /**
     * @Rest\Route("/jobs/{id}", name="jobs_update", methods={"PUT"}, options={"method_prefix"=false, "expose"=true })
     * @return void|JsonResponse
     */
    public function updateJob()
    {
        $job_id = $this->request->getMasterRequest()->attributes->get('id');
        $job = $this->jobRepository->find($job_id);

        if (!$job){
            $message = ['code' => 404, 'message' => 'resource not found'];
            $message = $this->serializer->serialize($message, 'json');
            return new JsonResponse($message, Response::HTTP_NOT_FOUND, [], true);
        }


        $postData = $this->request->getMasterRequest()->request->all();
        foreach ($postData as $key => $value) {
            $this->setDataIntoObject($key, $value, $job);
            $this->entityManager->persist($job);
        }
        $this->entityManager->flush();

        $message = ['code' => 200, 'message' => 'resource updated successfully'];
        $message = $this->serializer->serialize($message, 'json');

        return new JsonResponse($message, Response::HTTP_OK, [], true);
    }

    public function setDataIntoObject($key, $value, Job $objet)
    {
        $key_value = [
            'title' => 'title',
            'description' => 'description',
            'priceMinimum' => 'priceMinimum',
            'priceMaximum' => 'priceMaximum'
        ];

        if (array_key_exists($key, $key_value)) {

            switch ($key) {
                case $key === "title":
                    $objet->setTitle($value);
                    break;
                case $key === "description":
                    $objet->setTextDescription($value);
                    break;
                case $key === "priceMinimum":
                    $objet->setPriceMinimum($value);
                    break;
                case $key === "priceMaximum":
                    $objet->setPriceMaximum($value);
                    break;
                default:
                    break;
            }
        }
    }

}
