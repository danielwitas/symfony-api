<?php


namespace App\Exception;

use App\Api\ApiProblem;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class ApiProblemException extends HttpException
{
    private $apiProblem;

    public function __construct(ApiProblem $apiProblem, Throwable $previous = null)
    {
        $this->apiProblem = $apiProblem;
        $message = $apiProblem->getTitle();
        $code = $apiProblem->getStatusCode();
        parent::__construct($code, $message, $previous);
    }

    public function getApiProblem()
    {
        return $this->apiProblem;
    }

}