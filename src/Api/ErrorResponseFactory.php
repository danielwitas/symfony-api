<?php


namespace App\Api;


use Symfony\Component\HttpFoundation\JsonResponse;

class ErrorResponseFactory
{
    public function createResponse(ApiProblem $apiProblem)
    {
        $data = $apiProblem->toArray();
        $response = new JsonResponse(
            $data,
            $apiProblem->getStatusCode()
        );
        $response->headers->set('Content-Type', 'application/problem+json');
        return $response;
    }
}