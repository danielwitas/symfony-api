<?php

namespace App\Controller;

use App\Services\ProductResourceManager;
use App\Services\TemplateResourceManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/templates")
 */
class TemplateController extends AbstractController
{

    private $templateResourceManager;
    private $productResourceManager;

    public function __construct(TemplateResourceManager $templateResourceManager, ProductResourceManager $productResourceManager)
    {
        $this->templateResourceManager = $templateResourceManager;
        $this->productResourceManager = $productResourceManager;
    }
    /**
     * @Route("/{id}", name="templates_get_item", methods="GET", requirements={"id"="\d+"})
     */
    public function item(int $id): Response
    {
        $template = $this->templateResourceManager->getSingleTemplate($id);
        return $this->json($template);
    }

    /**
     * @Route(name="templates_get_collection", methods="GET")
     */
    public function collection(Request $request): Response
    {
        $collection = $this->templateResourceManager->getTemplateCollection($request);
        return $this->json($collection);
    }

    /**
     * @Route("/{id}", name="templates_delete_item", methods="DELETE", requirements={"id"="\d+"})
     */
    public function delete(int $id): Response
    {
        $this->templateResourceManager->deleteTemplate($id);
        return $this->json(['info' => 'Template has been deleted']);
    }

    /**
     * @Route(name="templates_post_item", methods="POST")
     */
    public function post(Request $request): Response
    {
        $this->templateResourceManager->addTemplate($request);
        return $this->json(['info' => 'Template has been added'], Response::HTTP_CREATED);
    }

    /**
     * @Route("/{id}", name="templates_patch_item", methods="PATCH", requirements={"id"="\d+"})
     */
    public function patch(Request $request, int $id): Response
    {
        $this->templateResourceManager->updateTemplate($request, $id);
        return $this->json(['info' => 'Template has been updated']);
    }

    /**
     * @Route("/{id}/products", name="templates_post_products", methods="POST", requirements={"id"="\d+"})
     */
    public function postTemplateProduct(Request $request, int $id): Response
    {
        $this->productResourceManager->addTemplateProduct($request, $id);
        return $this->json(['info' => 'Product has been added'], Response::HTTP_CREATED);
    }

    /**
     * @Route("/{id}/products", name="templates_get_products_collection", methods="GET", requirements={"id"="\d+"})
     */
    public function getTemplateProducts(int $id): Response
    {
        $products = $this->productResourceManager->getTemplateProducts($id);
        return $this->json($products);
    }


}
