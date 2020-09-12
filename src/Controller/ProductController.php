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
    public function item(Product $product)
    {
        return $this->json(
            $product,
            Response::HTTP_OK,
            ['Content-Type' => 'application/json']
        );
    }

    /**
     * @Route("/products", name="products_get_collection", methods="GET")
     */
    public function collection()
    {
        $products = $this->entityManager->getRepository(Product::class)->findAll();
        return $this->json(
            $products,
            Response::HTTP_OK,
            ['Content-Type' => 'application/json']
        );
    }

    /**
     * @Route("/products/{id}", name="products_delete_item", methods="DELETE", requirements={"id"="\d+"})
     */
    public function delete(Product $product)
    {
        $this->entityManager->remove($product);
        $this->entityManager->flush();
        return $this->json(
            ['info' => 'Product has been deleted.'],
            Response::HTTP_OK,
            ['Content-Type' => 'application/json']
        );
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

        $user = $this->entityManager->getRepository(User::class)->find(4);
        $form->getData()->setOwner($user);

        $this->entityManager->persist($form->getData());
        $this->entityManager->flush();
        return $this->json(
            ['info' => 'Product has been added.'],
            Response::HTTP_CREATED,
            ['Content-Type' => 'application/json']
        );

    }

    /**
     * @Route("/products/{id}", name="products_put_item", methods="PUT", requirements={"id"="\d+"})
     */
    public function put(Request $request, Product $product)
    {
        //$this->denyAccessUnlessGranted('ROLE_USER');
        $data = $this->jsonDecode($request->getContent());
        $this->isJsonValid($data);
        $existingProduct = $this->entityManager->getRepository(Product::class)->find($product->getId());
        $form = $this->createForm(ProductType::class, $existingProduct);
        $form->submit($data);
        $this->isFormValid($form);

        $user = $this->entityManager->getRepository(User::class)->find($this->getUser()->getId());
        $form->getData()->setOwner($user);
        $this->entityManager->flush();
        return $this->json(
            ['info' => 'Product has been updated.'],
            Response::HTTP_OK,
            ['Content-Type' => 'application/json']
        );
    }

    /**
     * @Route("/products/{id}", name="products_patch_item", methods="PATCH", requirements={"id"="\d+"})
     */
    public function patch(Request $request, Product $product)
    {

        $data = $this->jsonDecode($request->getContent());
        $this->isJsonValid($data);
        $existingProduct = $this->entityManager->getRepository(Product::class)->find($product->getId());
        $form = $this->createForm(ProductType::class, $existingProduct);
        $this->denyAccessUnlessGranted('EDIT', $existingProduct);
        $form->submit($data, false);
        $this->isFormValid($form);
        $this->entityManager->flush();
        return $this->json(
            ['info' => 'Product has been updated.'],
            Response::HTTP_OK,
            ['Content-Type' => 'application/json']
        );
    }
}
