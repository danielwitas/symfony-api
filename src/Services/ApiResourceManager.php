<?php

namespace App\Services;

use App\Pagination\PaginationFactory;
use App\Validation\ApiValidator;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ApiResourceManager
{
    protected $entityManager;
    protected $paginationFactory;
    protected $token;
    protected $container;
    protected $apiValidator;


    public function __construct(
        EntityManagerInterface $entityManager,
        PaginationFactory $paginationFactory,
        TokenStorageInterface $token,
        ContainerInterface $container,
        ApiValidator $apiValidator
    )
    {
        $this->entityManager = $entityManager;
        $this->paginationFactory = $paginationFactory;
        $this->token = $token;
        $this->container = $container;
        $this->apiValidator = $apiValidator;
    }

    protected function createForm(string $type, $data = null, array $options = []): FormInterface
    {
        return $this->container->get('form.factory')->create($type, $data, $options);
    }

    protected function denyAccessUnlessGranted($attribute, $subject = null, string $message = 'Access Denied.'): void
    {
        if (!$this->isGranted($attribute, $subject)) {
            throw new AccessDeniedException($message);
        }
    }

    protected function isGranted($attribute, $subject = null): bool
    {
        if (!$this->container->has('security.authorization_checker')) {
            throw new \LogicException('The SecurityBundle is not registered in your application. Try running "composer require symfony/security-bundle".');
        }
        return $this->container->get('security.authorization_checker')->isGranted($attribute, $subject);
    }

    protected function createNotFoundException(string $message = 'Not Found', \Throwable $previous = null): NotFoundHttpException
    {
        return new NotFoundHttpException($message, $previous);
    }


}