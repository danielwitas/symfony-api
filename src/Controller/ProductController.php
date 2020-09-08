<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\User;
use App\Form\ProductType;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    private $serializer;
    private $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer
    )
    {
        $this->serializer = $serializer;
        $this->entityManager = $entityManager;
    }


    /**
     * @Route("/products", name="products_post_item", methods="POST")
     */
    public function addProduct(Request $request)
    {
        $product = json_decode(
            $request->getContent(),
            true
        );
        if(null === $product) {
            return $this->json(
                ['Error' => 'Invalid Json'],
                Response::HTTP_BAD_REQUEST,
                ['Content-Type' => 'application/json']);
        }
        $form = $this->createForm(ProductType::class, new Product());
        $form->submit($product);

        if (false === $form->isValid()) {
            $data = [
                'status' => 'error',
                'errors' => $form->getErrors()
            ];
            return new Response(
                $this->serializer->serialize($data, 'json'),
                Response::HTTP_BAD_REQUEST,
                ['Content-Type' => 'application/json']

            );
        }
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
     * @Route("/products", name="products_get_collection", methods="GET")
     */
    public function listProduct()
    {
        $products = $this->entityManager->getRepository(Product::class)->findAll();
        return new Response(
            $this->serializer->serialize($products, 'json', SerializationContext::create()->enableMaxDepthChecks()),
            Response::HTTP_OK,
            ['Content-Type' => 'application/json']
        );
    }

    /**
     * @Route("/products/{id}", name="products_delete_item", methods="DELETE", requirements={"id"="\d+"})
     */
    public function deleteProduct(Product $product)
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
     * @Route("/products/{id}", name="products_get_item", methods="GET", requirements={"id"="\d+"})
     */
    public function getProduct(Product $product)
    {
        return new Response(
            $this->serializer->serialize($product, 'json', SerializationContext::create()->enableMaxDepthChecks()),
            Response::HTTP_OK,
            ['Content-Type' => 'application/json']
        );
    }

    /**
     * @Route("/products/{id}", name="products_put_item", methods="PUT", requirements={"id"="\d+"})
     */
    public function putProduct(Request $request, Product $product)
    {

        $data = json_decode($request->getContent(), true);
        if(null === $data) {
            return $this->json(
                ['Error' => 'Invalid Json'],
                Response::HTTP_BAD_REQUEST,
                ['Content-Type' => 'application/json']);
        }
        $existingProduct = $this->entityManager->getRepository(Product::class)->find($product->getId());
        $form = $this->createForm(ProductType::class, $existingProduct);
        $form->submit($data);

        if (false === $form->isValid()) {
            $response = [
                'status' => 'error',
                'errors' => $form->getErrors()
            ];
            return new Response(
                $this->serializer->serialize($response, 'json'),
                Response::HTTP_BAD_REQUEST,
                ['Content-Type' => 'application/json']

            );
        }
        $user = $this->entityManager->getRepository(User::class)->find(4);
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
    public function patchProduct(Request $request, Product $product)
    {
        $data = json_decode($request->getContent(), true);
        if(null === $data) {
            return $this->json(
                ['Error' => 'Invalid Json'],
                Response::HTTP_BAD_REQUEST,
                ['Content-Type' => 'application/json']);
        }
        $existingProduct = $this->entityManager->getRepository(Product::class)->find($product->getId());
        $form = $this->createForm(ProductType::class, $existingProduct);
        $form->submit($data, false);
        if (false === $form->isValid()) {
            $response = [
                'status' => 'error',
                'errors' => $form->getErrors()
            ];
            return new Response(
                $this->serializer->serialize($response, 'json'),
                Response::HTTP_BAD_REQUEST,
                ['Content-Type' => 'application/json']

            );
        }

        $this->entityManager->flush();
        return $this->json(
            ['info' => 'Product has been updated.'],
            Response::HTTP_OK,
            ['Content-Type' => 'application/json']
        );
    }
}
