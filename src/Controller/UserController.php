<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\User;
use App\Form\ChangePasswordType;
use App\Form\PasswordResetType;
use App\Form\UserType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends ApiController
{
    /**
     * @Route("/users/{id}", name="users_get_item", methods="GET", requirements={"id"="\d+"})
     */
    public function item(int $id)
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['id' => $id]);
        if (!$user) {
            throw $this->createNotFoundException(sprintf('No user found with id %d', $id));
        }
        return $this->createApiResponse($user);
    }

    /**
     * @Route("/users", name="users_get_collection", methods="GET")
     */
    public function collection(Request $request)
    {
        $filter = $request->query->get('filter');
        $qb = $this->entityManager->getRepository(User::class)->findAllQueryBuilder($filter);
        $paginatedCollection = $this->paginationFactory->createCollection($qb, $request, 'users_get_collection');
        return $this->createApiResponse($paginatedCollection->getResult('users'));
    }

    /**
     * @Route("/users/{id}", name="users_delete_item", methods="DELETE", requirements={"id"="\d+"})
     */
    public function delete(int $id)
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['id' => $id]);
        if (!$user) {
            throw $this->createNotFoundException(sprintf('No user found with id %d', $id));
        }
        $this->entityManager->remove($user);
        $this->entityManager->flush();
        return $this->createApiResponse(['info' => 'User has been deleted.']);
    }

    /**
     * @Route("/users", name="users_post_item", methods="POST")
     */
    public function post(Request $request)
    {
        $data = $this->jsonDecode($request->getContent());
        $this->isJsonValid($data);
        $form = $this->createForm(UserType::class, new User());
        $form->submit($data);
        $this->isFormValid($form);

        /** @var User $user */
        $user = $form->getData();
        $user->setConfirmationToken($this->tokenGenerator->getRandomSecureToken());
        $user->setEnabled(false);
        $user->setPassword(
            $this->userPasswordEncoder->encodePassword($user, $user->getPassword())
        );
        $this->entityManager->persist($user);
        $this->entityManager->flush();
        $this->mailer->sendConfirmationEmail($user);
        return $this->createApiResponse([
            'info' => 'Success. Check your e-mail and confirm your accout to complete registration'
        ], Response::HTTP_CREATED);
    }

    /**
     * @Route("/users/{id}/products", name="users_products", methods="GET")
     */
    public function userProducts(int $id)
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['id' => $id]);
        if (!$user) {
            throw $this->createNotFoundException(sprintf('No user found with id %d', $id));
        }
        $products = $this->entityManager->getRepository(Product::class)->findBy(['owner' => $user]);
        return $this->createApiResponse($products);
    }

}
