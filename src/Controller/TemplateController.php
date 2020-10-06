<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/templates")
 */
class TemplateController extends ApiController
{
    /**
     * @Route("/{id}", name="templates_get_item", methods="GET", requirements={"id"="\d+"})
     */
    public function item(int $id): Response
    {
        $template = $this->templateManager->getSingleTemplate($id);
        return $this->createApiResponse($template);
    }

    /**
     * @Route(name="templates_get_collection", methods="GET")
     */
    public function collection(Request $request): Response
    {
        $collection = $this->templateManager->getTemplateCollection($request);
        return $this->createApiResponse($collection);
    }

    /**
     * @Route("/{id}", name="templates_delete_item", methods="DELETE", requirements={"id"="\d+"})
     */
    public function delete(int $id): Response
    {
        $this->templateManager->deleteTemplate($id);
        return $this->createApiResponse(['info' => 'Template has been deleted']);
    }

    /**
     * @Route(name="templates_post_item", methods="POST")
     */
    public function post(Request $request): Response
    {
        $this->templateManager->addTemplate($request);
        return $this->createApiResponse(['info' => 'Template has been added'], Response::HTTP_CREATED);
    }

    /**
     * @Route("/{id}", name="templates_patch_item", methods="PATCH", requirements={"id"="\d+"})
     */
    public function patch(Request $request, int $id): Response
    {
        $this->templateManager->updateTemplate($request, $id);
        return $this->createApiResponse(['info' => 'Template has been updated']);
    }

    /**
     * @Route("/{id}/products", name="templates_post_products", methods="POST", requirements={"id"="\d+"})
     */
    public function postTemplateProduct(Request $request, int $id): Response
    {
        $this->productManager->addTemplateProduct($request, $id);
        return $this->createApiResponse(['info' => 'Product has been added'], Response::HTTP_CREATED);
    }

    /**
     * @Route("/{id}/products", name="templates_get_products_collection", methods="GET", requirements={"id"="\d+"})
     */
    public function getTemplateProducts(int $id): Response
    {
        $products = $this->productManager->getTemplateProducts($id);
        return $this->createApiResponse($products);
    }


}
