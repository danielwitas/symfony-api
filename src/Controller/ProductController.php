<?php

namespace App\Controller;

use App\Services\ProductResourceManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/products")
 */
class ProductController extends AbstractController
{

    private $productResourceManager;

    public function __construct(ProductResourceManager $productResourceManager)
    {
        $this->productResourceManager = $productResourceManager;
    }
    /**
     * @Route("/{id}", name="products_get_item", methods="GET", requirements={"id"="\d+"})
     */
    public function item(int $id): Response
    {
        $product = $this->productResourceManager->getSingleProduct($id);
        return $this->json($product);
    }

    /**
     * @Route(name="products_get_collection", methods="GET")
     */
    public function collection(Request $request): Response
    {
        $collection = $this->productResourceManager->getProductCollection($request);
        return $this->json($collection);
    }

    /**
     * @Route("/{id}", name="products_delete_item", methods="DELETE", requirements={"id"="\d+"})
     */
    public function delete(int $id): Response
    {
        $this->productResourceManager->deleteProduct($id);
        return $this->json([
            'info' => $this->productResourceManager::PRODUCT_DELETED_MESSAGE
        ]);
    }

    /**
     * @Route(name="products_post_item", methods="POST")
     */
    public function post(Request $request): Response
    {
        $this->productResourceManager->addUserProduct($request);
        return $this->json([
            'info' => $this->productResourceManager::PRODUCT_ADDED_MESSAGE],
            Response::HTTP_CREATED
        );
    }

    /**
     * @Route("/{id}", name="products_patch_item", methods="PATCH", requirements={"id"="\d+"})
     */
    public function patch(Request $request, int $id): Response
    {
        $this->productResourceManager->updateProduct($request, $id);
        return $this->json([
            'info' => $this->productResourceManager::PRODUCT_UPDATED_MESSAGE
        ]);
    }
}
