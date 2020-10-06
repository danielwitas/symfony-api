<?php


namespace App\EventListener;


use App\Api\ApiProblem;
use App\Api\ErrorResponseFactory;
use App\Exception\ApiProblemException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\RouterInterface;

class ApiExceptionSubscriber implements EventSubscriberInterface
{
    private $responseFactory;
    private $router;
    private $parameterBag;

    public function __construct(
        ErrorResponseFactory $responseFactory,
        RouterInterface $router,
        ParameterBagInterface $parameterBag)
    {
        $this->responseFactory = $responseFactory;
        $this->router = $router;
        $this->parameterBag = $parameterBag;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::EXCEPTION => ['onKernelException']
        ];
    }

    public function onKernelException(ExceptionEvent $event)
    {
        if ($this->parameterBag->get('kernel.debug')) {
            return;
        }
        $e = $event->getThrowable();
        $statusCode = $e instanceof HttpExceptionInterface ? $e->getStatusCode() : 500;
        if ($e instanceof ApiProblemException) {
            $apiProblem = $e->getApiProblem();
        } else {
            $apiProblem = new ApiProblem($statusCode);
            if ($e instanceof HttpExceptionInterface) {
                $apiProblem->set('detail', $e->getMessage());
            }
        }
        $response = $this->responseFactory->createResponse($apiProblem);
        $event->setResponse($response);
    }
}