<?php

namespace App\Services;

use App\Entity\Product;
use App\Entity\Template;
use App\Form\ProductType;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\HttpFoundation\Request;

class ProductResourceManager extends ApiResourceManager
{

    const PRODUCT_DELETED_MESSAGE = 'Product has been deleted';
    const PRODUCT_UPDATED_MESSAGE = 'Product has been updated';
    const PRODUCT_ADDED_MESSAGE = 'Product has been added';

    public function getSingleProduct(int $id): Product
    {
        return $this->findProductById($id);
    }

    public function getTemplateProducts(int $id): Collection
    {
        return $this->findTemplateById($id)->getProducts();
    }

    public function getProductCollection(Request $request): array
    {
        $this->denyAccessUnlessGranted('ROLE_USER', null, 'You have to be logged in to browse products');
        $filter = $request->query->get('filter');
        $user = $this->token->getToken()->getUser();
        $qb = $this->entityManager->getRepository(Product::class)->findAllQueryBuilder($user, $filter);
        $paginatedCollection = $this->paginationFactory->createCollection($qb, $request, 'products_get_collection');
        return $paginatedCollection->getResult('products');
    }

    public function deleteProduct(int $id): void
    {
        $product = $this->findProductById($id);
        $this->entityManager->remove($product);
        $this->entityManager->flush();
    }

    public function addUserProduct(Request $request): void
    {
        $this->denyAccessUnlessGranted('ROLE_USER', null, 'You have to be logged in to add products');
        $form = $this->createForm(ProductType::class, new Product());
        $product = $this->apiValidator->processForm($request, $form);
        if (!$product->getWeight()) {
            $product->setWeight(100);
        }
        $user = $this->token->getToken()->getUser();
        $product->setOwner($user);
        $this->entityManager->persist($product);
        $this->entityManager->flush();
    }

    public function updateProduct(Request $request, int $id): void
    {
        $existingProduct = $this->findProductById($id);
        $form = $this->createForm(ProductType::class, $existingProduct);
        $this->apiValidator->processForm($request, $form);
        $this->entityManager->flush();
    }

    public function addTemplateProduct(Request $request, int $id): void
    {
        $template = $this->findTemplateById($id);
        $form = $this->createForm(ProductType::class, new Product());
        $product = $this->apiValidator->processForm($request, $form);
        $user = $this->token->getToken()->getUser();
        $product->setOwner($user);
        $product->setTemplate($template);
        if (!$product->getWeight()) {
            $product->setWeight(100);
        }
        $this->entityManager->persist($product);
        $this->entityManager->flush();
    }

    private function findProductById(int $id): Product
    {
        /** @var Product $product */
        $product = $this->entityManager->getRepository(Product::class)->findOneBy(['id' => $id]);
        if (!$product) {
            throw $this->createNotFoundException('Product does not exist');
        }
        $this->denyAccessUnlessGranted('OWNER', $product, 'This product does not belong to you');
        return $product;
    }

    private function findTemplateById(int $id): Template
    {
        /** @var Template $template */
        $template = $this->entityManager->getRepository(Template::class)->findOneBy(['id' => $id]);
        if (!$template) {
            throw $this->createNotFoundException('Template does not exist');
        }
        $this->denyAccessUnlessGranted('OWNER', $template, 'This template does not belong to you');
        return $template;
    }



}