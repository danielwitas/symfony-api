<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\Template;
use App\Form\ProductType;
use App\Form\TemplateType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TemplateController extends ApiController
{
    /**
     * @Route("/templates/{id}", name="templates_get_item", methods="GET", requirements={"id"="\d+"})
     */
    public function item(int $id)
    {
        $template = $this->entityManager->getRepository(Template::class)->findOneBy(['id' => $id]);
        if(!$template) {
            throw $this->createNotFoundException(sprintf('No template found with id %d', $id));
        }
        return $this->createApiResponse($template);
    }

    /**
     * @Route("/templates", name="templates_get_collection", methods="GET")
     */
    public function collection(Request $request)
    {
        $filter = $request->query->get('filter');
        $qb = $this->entityManager->getRepository(Template::class)->findAllQueryBuilder($filter);
        $paginatedCollection = $this->paginationFactory->createCollection($qb, $request, 'templates_get_collection');
        return $this->createApiResponse($paginatedCollection->getResult('templates'));
    }

    /**
     * @Route("/templates/{id}", name="templates_delete_item", methods="DELETE", requirements={"id"="\d+"})
     */
    public function delete(int $id)
    {
        $template = $this->entityManager->getRepository(Template::class)->findOneBy(['id' => $id]);
        $this->denyAccessUnlessGranted('OWNER', $template);
        if(!$template) {
            throw $this->createNotFoundException(sprintf('No template found with id %d', $id));
        }
        $this->entityManager->remove($template);
        $this->entityManager->flush();
        return $this->createApiResponse(['info' => 'Template has been deleted']);
    }

    /**
     * @Route("/templates", name="templates_post_item", methods="POST")
     */
    public function post(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $data = $this->jsonDecode($request->getContent());
        $this->isJsonValid($data);
        $form = $this->createForm(TemplateType::class, new Template());
        $form->submit($data);
        $this->isFormValid($form);

        $user = $this->getUser();
        $form->getData()->setOwner($user);

        $this->entityManager->persist($form->getData());
        $this->entityManager->flush();
        return $this->createApiResponse(['info' => 'Template has been added'], Response::HTTP_CREATED);
    }

    /**
     * @Route("/templates/{id}", name="templates_patch_item", methods="PATCH", requirements={"id"="\d+"})
     */
    public function patch(Request $request, int $id)
    {
        $existingTemplate = $this->entityManager->getRepository(Template::class)->findOneBy(['id' => $id]);
        $this->denyAccessUnlessGranted('OWNER', $existingTemplate);
        if(!$existingTemplate) {
            throw $this->createNotFoundException(sprintf('No template found with id %d', $id));
        }
        $data = $this->jsonDecode($request->getContent());
        $this->isJsonValid($data);
        $form = $this->createForm(TemplateType::class, $existingTemplate);
        $form->submit($data);
        $this->isFormValid($form);
        $this->entityManager->flush();
        return $this->createApiResponse(['info' => 'Template has been updated']);
    }

    /**
     * @Route("/templates/{id}/products", name="templates_post_products", methods="POST", requirements={"id"="\d+"})
     */
    public function postProducts(Request $request, int $id)
    {
        $template = $this->entityManager->getRepository(Template::class)->findOneBy(['id' => $id]);
        $this->denyAccessUnlessGranted('OWNER', $template);
        if(!$template) {
            throw $this->createNotFoundException(sprintf('No template found with id %d', $id));
        }
        $data = $this->jsonDecode($request->getContent());
        $this->isJsonValid($data);
        $form = $this->createForm(ProductType::class, new Product());
        $form->submit($data);
        $this->isFormValid($form);
        /** @var Product $product */
        $product = $form->getData();
        $product->setTemplate($template);
        $user = $this->getUser();
        $product->setOwner($user);
        $this->entityManager->persist($product);
        $this->entityManager->flush();
        return $this->createApiResponse(['info' => 'Product has been added'], Response::HTTP_CREATED);

    }

    /**
     * @Route("/templates/{id}/products", name="templates_get_products_collection", methods="GET", requirements={"id"="\d+"})
     */
    public function getProducts(int $id)
    {
        /** @var Template $template */
        $template = $this->entityManager->getRepository(Template::class)->findOneBy(['id' => $id]);
        $this->denyAccessUnlessGranted('OWNER', $template);
        if(!$template) {
            throw $this->createNotFoundException(sprintf('No product found with id %d', $id));
        }
        return $this->createApiResponse($template->getProducts());
    }




}
