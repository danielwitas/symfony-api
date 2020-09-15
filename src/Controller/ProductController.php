<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\User;
use App\Form\ProductType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends ApiController
{
    /**
     * @Route("/products/{id}", name="products_get_item", methods="GET", requirements={"id"="\d+"})
     */
    public function item(int $id)
    {
        $product = $this->entityManager->getRepository(Product::class)->findOneBy(['id' => $id]);
        if(!$product) {
            throw $this->createNotFoundException(sprintf('No product found with id %d', $id));
        }
        return $this->createApiResponse($product);
    }

    /**
     * @Route("/products", name="products_get_collection", methods="GET")
     */
    public function collection(Request $request)
    {
        $filter = $request->query->get('filter');
        $qb = $this->entityManager->getRepository(Product::class)->findAllQueryBuilder($filter);
        $paginatedCollection = $this->paginationFactory->createCollection($qb, $request, 'products_get_collection');
        return $this->createApiResponse($paginatedCollection->getResult('products'));
    }

    /**
     * @Route("/products/{id}", name="products_delete_item", methods="DELETE", requirements={"id"="\d+"})
     */
    public function delete(int $id)
    {
        $product = $this->entityManager->getRepository(Product::class)->findOneBy(['id' => $id]);
        $this->denyAccessUnlessGranted('OWNER', $product);
        if(!$product) {
            throw $this->createNotFoundException(sprintf('No product found with id %d', $id));
        }
        $this->entityManager->remove($product);
        $this->entityManager->flush();
        return $this->createApiResponse(['info' => 'Product has been deleted']);
    }

    /**
     * @Route("/products", name="products_post_item", methods="POST")
     */
    public function post(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $data = $this->jsonDecode($request->getContent());
        $this->isJsonValid($data);
        $form = $this->createForm(ProductType::class, new Product());
        $form->submit($data);
        $this->isFormValid($form);

        $user = $this->getUser();
        $form->getData()->setOwner($user);

        $this->entityManager->persist($form->getData());
        $this->entityManager->flush();
        return $this->createApiResponse(['info' => 'Product has been added'], Response::HTTP_CREATED);
    }

    /**
     * @Route("/products/{id}", name="products_patch_item", methods="PATCH", requirements={"id"="\d+"})
     */
    public function patch(Request $request, int $id)
    {
        $existingProduct = $this->entityManager->getRepository(Product::class)->findOneBy(['id'=>$id]);
        $this->denyAccessUnlessGranted('OWNER', $existingProduct);
        if(!$existingProduct) {
            throw $this->createNotFoundException(sprintf('No product found with id %d', $id));
        }
        $data = $this->jsonDecode($request->getContent());
        $this->isJsonValid($data);
        $form = $this->createForm(ProductType::class, $existingProduct);
        $form->submit($data, false);
        $this->isFormValid($form);
        $this->entityManager->flush();
        return $this->createApiResponse(['info' => 'Product has been updated']);
    }
}
