<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/{vueRouting}", name="home", requirements={"route"="^.+"})
     * @return Response
     */
    public function home()
    {
        return $this->render('home/index.html.twig', []);
    }

    /**
     * @Route("/", name="index", requirements={"route"="^.+"})
     * @return Response
     */
    public function index()
    {
        return $this->render('home/index.html.twig', []);
    }

}
