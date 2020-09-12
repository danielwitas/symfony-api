<?php


namespace App\EventListener;


use App\Api\ApiProblem;
use App\Exception\ApiProblemException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\User\UserInterface;

class ApiExceptionSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::EXCEPTION => ['onKernelException']
        ];
    }

    public function onKernelException(ExceptionEvent $event)
    {
        $e = $event->getThrowable();
        if($e instanceof ApiProblemException) {
            $apiProblem = $e->getApiProblem();
        } else {
            $statusCode = $e instanceof HttpExceptionInterface ? $e->getStatusCode() : 500;
            $apiProblem = new ApiProblem($statusCode);
            if($e instanceof UserInterface) {
                if($e->getMessage() === 'App\\Entity\\User object not found by the @ParamConverter annotation.')
                $apiProblem->set('detail', 'User not found.');
            }

        }
        $response = new JsonResponse(
            $apiProblem->toArray(),
            $apiProblem->getStatusCode(),
            ['Content-Type', 'application/problem+json']
        );
        $event->setResponse($response);
    }
}