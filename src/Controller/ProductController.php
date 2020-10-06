<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/products")
 */
class ProductController extends ApiController
{
    /**
     * @Route("/{id}", name="products_get_item", methods="GET", requirements={"id"="\d+"})
     */
    public function item(int $id): Response
    {
        $product = $this->productManager->getSingleProduct($id);
        return $this->createApiResponse($product);
    }

    /**
     * @Route(name="products_get_collection", methods="GET")
     */
    public function collection(Request $request): Response
    {
        $collection = $this->productManager->getProductCollection($request);
        return $this->createApiResponse($collection);
    }

    /**
     * @Route("/{id}", name="products_delete_item", methods="DELETE", requirements={"id"="\d+"})
     */
    public function delete(int $id): Response
    {
        $this->productManager->deleteProduct($id);
        return $this->createApiResponse([
            'info' => $this->productManager::PRODUCT_DELETED_MESSAGE
        ]);
    }

    /**
     * @Route(name="products_post_item", methods="POST")
     */
    public function post(Request $request): Response
    {
        $this->productManager->addUserProduct($request);
        return $this->createApiResponse([
            'info' => $this->productManager::PRODUCT_ADDED_MESSAGE],
            Response::HTTP_CREATED
        );
    }

    /**
     * @Route("/{id}", name="products_patch_item", methods="PATCH", requirements={"id"="\d+"})
     */
    public function patch(Request $request, int $id): Response
    {
        $this->productManager->updateProduct($request, $id);
        return $this->createApiResponse([
            'info' => $this->productManager::PRODUCT_UPDATED_MESSAGE
        ]);
    }
}
