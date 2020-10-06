<?php

namespace App\EventListener;

use App\Api\ApiProblem;
use App\Api\ErrorResponseFactory;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationFailureEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTNotFoundEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;

class AuthenticationFailureSubscriber implements EventSubscriberInterface
{

    private $errorResponseFactory;

    public function __construct(ErrorResponseFactory $errorResponseFactory)
    {
        $this->errorResponseFactory = $errorResponseFactory;
    }

    public static function getSubscribedEvents()
    {
        return [
            'lexik_jwt_authentication.on_authentication_failure' => ['onAuthenticationFailure'],
            'lexik_jwt_authentication.on_jwt_not_found' => ['onMissingToken']
        ];
    }

    public function onAuthenticationFailure(AuthenticationFailureEvent $event)
    {
        $apiProblem = new ApiProblem(Response::HTTP_UNAUTHORIZED, ApiProblem::TYPE_INVALID_CREDENTIALS);
        $apiProblem->set('detail', 'Bad credentials, please verify that your username/password are correctly set');
        $response = $this->errorResponseFactory->createResponse($apiProblem);
        $event->setResponse($response);
    }

    public function onMissingToken(JWTNotFoundEvent $event)
    {
        $apiProblem = new ApiProblem(Response::HTTP_UNAUTHORIZED, ApiProblem::TYPE_TOKEN_NOT_FOUND);
        $apiProblem->set('detail', 'Could not find JWT token');
        $response = $this->errorResponseFactory->createResponse($apiProblem);
        $event->setResponse($response);
    }


}