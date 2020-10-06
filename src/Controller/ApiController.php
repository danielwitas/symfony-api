<?php

namespace App\Controller;

use App\Services\ProductResourceManager;
use App\Services\TemplateResourceManager;
use App\Services\UserResourceManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class ApiController extends AbstractController
{
    protected $productManager;
    protected $templateManager;
    protected $userResourceManager;

    public function __construct(
        ProductResourceManager $productManager,
        TemplateResourceManager $templateManager,
        UserResourceManager $userResourceManager
    )
    {
        $this->productManager = $productManager;
        $this->templateManager = $templateManager;
        $this->userResourceManager = $userResourceManager;
    }

    protected function createApiResponse($data, $statusCode = Response::HTTP_OK)
    {
        return $this->json($data, $statusCode, ['Content-Type' => 'application/json']);
    }
}