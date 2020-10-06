<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/users")
 */
class UserController extends ApiController
{
    /**
     * @Route("/{id}", name="users_get_item", methods="GET", requirements={"id"="\d+"})
     */
    public function item(int $id): Response
    {
        $user = $this->userResourceManager->getSingleUser($id);
        return $this->createApiResponse($user);
    }

    /**
     * @Route(name="users_get_collection", methods="GET")
     */
    public function collection(Request $request): Response
    {
        $collection = $this->userResourceManager->getUserCollection($request);
        return $this->createApiResponse($collection);
    }

    /**
     * @Route("/{id}", name="users_delete_item", methods="DELETE", requirements={"id"="\d+"})
     */
    public function delete(int $id): Response
    {
        $this->userResourceManager->deleteUser($id);
        return $this->createApiResponse(['info' => 'User has been deleted.']);
    }


    /**
     * @Route("/{id}/products", name="users_products", methods="GET")
     */
    public function userProducts(int $id): Response
    {
        $products = $this->userResourceManager->getUserProducts($id);
        return $this->createApiResponse($products);
    }

    /**
     * @Route("/{id}/templates", name="users_templates", methods="GET")
     */
    public function userTemplates(int $id): Response
    {
        $templates = $this->userResourceManager->getUserTemplates($id);
        return $this->createApiResponse($templates);
    }

}
