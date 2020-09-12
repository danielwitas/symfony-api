<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\User;
use App\Form\ChangePasswordType;
use App\Form\UserType;
use App\Pagination\PaginatedCollection;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends ApiController
{
    /**
     * @Route("/users/{id}", name="users_get_item", methods="GET", requirements={"id"="\d+"})
     */
    public function item(User $user)
    {
        $user = $this->serializer->serialize($user, 'json');
        return new Response($user, Response::HTTP_OK, ['Content-Type' => 'application/json']);
    }

    /**
     * @Route("/users", name="users_get_collection", methods="GET")
     */
    public function collection(Request $request)
    {

        $page = $request->query->get('page', 1);
        $qb = $this->entityManager->getRepository(User::class)->findAllQueryBuilder();
        $adapter = new QueryAdapter($qb);
        $pagerfanta = new Pagerfanta($adapter);
        $pagerfanta->setCurrentPage($page);
        $pagerfanta->getCurrentPageResults();
        $users = [];
        foreach ($pagerfanta->getCurrentPageResults() as $user) {
            $users[] = $user;
        }
        $paginatedCollection = new PaginatedCollection(
            $users,
            $pagerfanta->getNbResults()
        );
        $route = 'users_get_collection';
        $routeParams = [];
        $createLinkUrl = function ($targetPage) use ($route, $routeParams) {
          return $this->generateUrl($route, array_merge(
             $routeParams,
             ['page' => $targetPage]
          ));
        };
        $paginatedCollection->addLink('self', $createLinkUrl($page));
        $paginatedCollection->addLink('first', $createLinkUrl(1));
        $paginatedCollection->addLink('last', $createLinkUrl($pagerfanta->getNbPages()));

        if($pagerfanta->hasNextPage()) {
            $paginatedCollection->addLink('next', $createLinkUrl($pagerfanta->getNextPage()));
        }
        if($pagerfanta->hasPreviousPage()) {
            $paginatedCollection->addLink('prev', $createLinkUrl($pagerfanta->getNextPage()));
        }

        return $this->json(
            $paginatedCollection->getResult('users'),
            Response::HTTP_OK,
            ['Content-Type' => 'application/json']
        );
    }

    /**
     * @Route("/users/{id}", name="users_delete_item", methods="DELETE", requirements={"id"="\d+"})
     */
    public function delete(User $user)
    {
        $this->entityManager->remove($user);
        $this->entityManager->flush();
        return $this->json(
            ['info' => 'User has been deleted.'],
            Response::HTTP_OK,
            ['Content-Type' => 'application/json']
        );
    }

    /**
     * @Route("/users", name="users_post_item", methods="POST")
     */
    public function post(Request $request)
    {
//        for($i = 0; $i < 25; $i++) {
//            $user = new User();
//            $user->setUsername('daniel'.$i);
//            $user->setPassword('daniel'.$i);
//            $user->setEmail('daniel'.$i.'@daniel.com');
//            $this->entityManager->persist($user);
//        }
        $data = $this->jsonDecode($request->getContent());
        $this->isJsonValid($data);
        $form = $this->createForm(UserType::class, new User());
        $form->submit($data);
        $this->isFormValid($form);

        /** @var User $user */
        $user = $form->getData();
        $user->setPassword(
            $this->userPasswordEncoder->encodePassword($user, $user->getPassword())
        );
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $this->json(
            ['info' => 'User has been added.'],
            Response::HTTP_CREATED,
            ['Content-Type' => 'application/json']
        );
    }

    /**
     * @Route("/users/{id}/change-password", name="users_change_password", methods="PATCH")
     */
    public function changePassword(Request $request, User $user)
    {
        $data = $this->jsonDecode($request->getContent());
        $this->isJsonValid($data);
        $form = $this->createForm(ChangePasswordType::class, new User());
        $form->submit($data, false);
        $this->isFormValid($form);
        $checkPass = $this->userPasswordEncoder->isPasswordValid($user, $form->getData()->getPassword());
        if (!$checkPass) {
            return $this->json(
                ['error' => 'Invalid password.'],
                Response::HTTP_FORBIDDEN,
                ['Content-Type' => 'application/json']
            );
        }
        $user->setPassword(
            $this->userPasswordEncoder->encodePassword($user, $form->getData()->getNewPassword())
        );
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $this->json(
            ['info' => 'Password has been changed.'],
            Response::HTTP_OK,
            ['Content-Type' => 'application/json']
        );
    }

    /**
     * @Route("/users/{id}/set-role", name="users_set_role", methods="PATCH")
     */
    public function setRole(Request $request, User $user)
    {
        // add form
        // add voter
        return $this->json(['tba' => 'tba']);
    }

    /**
     * @Route("/users/{id}/change-email", name="users_change_email", methods="PATCH")
     */
    public function changeEmail(Request $request, User $user)
    {
        // add form
        // add voter
        return $this->json(['tba' => 'tba']);
    }

    /**
     * @Route("/users/{id}/products", name="users_products", methods="GET")
     */
    public function userProducts(User $user)
    {
        $products = $this->entityManager->getRepository(Product::class)->findBy(['owner' => $user]);
        // add form
        // add voter
        return $this->json($products);
    }




}
