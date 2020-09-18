<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ChangeRoleType;
use App\Form\UserType;
use JMS\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class HomePageController extends ApiController
{

    /**
     * @Route("/", name="home_page")
     */
    public function homepage(Request $request, SerializerInterface $serializer)
    {

        if($this->getUser()) {
            var_dump($this->getUser());
            $data = $serializer->serialize($this->getUser(), 'json');
        }
        $form = $this->createForm(ChangeRoleType::class);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {

            dd($form->getData());
        }
        return $this->render('base.html.twig', [
            'user' => null,
            'form' => $form->createView()
        ]);
    }
}
