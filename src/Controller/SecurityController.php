<?php


namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SecurityController extends ApiController
{

    /**
     * @Route("/login", name="app_login", methods={"POST"})
     */
    public function login()
    {
        if(!$this->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->json([
                'error' => 'Invalid login request: check that the Content-Type header is "application/json"'
            ], Response::HTTP_BAD_REQUEST);
        }
        return $this->json([
            'info' => 'You have logged in successfully.'
        ], Response::HTTP_OK);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        
    }
}