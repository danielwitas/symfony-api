<?php


namespace App\Controller;

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
        $this->redirectToRoute('home_page');
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

    /**
     * @Route("/confirm-user/{token}", name="app_confirm_user", methods="GET")
     */
    public function userRegisterConfirmation(string $token)
    {
        $this->userConfirmationService->confirmUser($token);
        return $this->redirectToRoute('home_page');
    }

    /**
     * @Route("/user-password-reset-confirm/{token}", name="app_confirm_password_reset", methods="GET")
     */
    public function userPasswordResetConfirm(string $token)
    {
        $this->userPasswordResetService->resetPassword($token);
        return $this->redirectToRoute('home_page');
    }


}