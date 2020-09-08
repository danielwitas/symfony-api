<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{

    private $serializer;
    private $entityManager;

    public function __construct(
        \JMS\Serializer\SerializerInterface $serializer,
        EntityManagerInterface $entityManager
    )
    {
        $this->serializer = $serializer;
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/users", name="users_get_collection", methods="GET")
     */
    public function getAllUsers()
    {
        $users = $this->entityManager->getRepository(User::class)->findAll();
        $users = $this->serializer->serialize($users, 'json');
        return new Response($users, Response::HTTP_OK, ['Content-Type' => 'application/json']);
    }

    /**
     * @Route("/users/{id}", name="users_get_item", methods="GET", requirements={"id"="\d+"})
     */
    public function getSingleUser(User $user)
    {
        $user = $this->serializer->serialize($user, 'json');
        return new Response($user, Response::HTTP_OK, ['Content-Type' => 'application/json']);
    }

    /**
     * @Route("/users/{id}", name="users_delete_item", methods="DELETE", requirements={"id"="\d+"})
     */
    public function deleteUser(User $user)
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
    public function addUser(Request $request)
    {

        $user = json_decode(
            $request->getContent(), true
        );

        if(null === $user) {
            return $this->json(
                ['Error' => 'Invalid Json'],
                Response::HTTP_BAD_REQUEST,
                ['Content-Type' => 'application/json']);
        }

        $form = $this->createForm(UserType::class, new User());

        $form->submit($user);

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
        $this->entityManager->persist($form->getData());
        $this->entityManager->flush();

        return $this->json(
            ['info' => 'User has been added.',],
            Response::HTTP_CREATED,
            ['Content-Type' => 'application/json']
        );
    }


}
