<?php


namespace App\Controller;

use App\Security\ChangeEmailService;
use App\Security\ChangeRoleService;
use App\Security\ChangePasswordService;
use App\Security\ResetPasswordService;
use App\Security\RegisterService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api")
 */
class SecurityController extends AbstractController
{

    /**
     * @Route("/change-password", name="security_change_password", methods="PATCH")
     */
    public function changePassword(Request $request, ChangePasswordService $passwordChangeService)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $passwordChangeService->changePassword($request, $this->getUser());
        return $this->json(['info' => 'Password has been changed.']);
    }

    /**
     * @Route("/password-reset", name="security_password_reset", methods="POST")
     */
    public function initResetPassword(Request $request, ResetPasswordService $resetPasswordService)
    {
        $resetPasswordService->initResetPassword($request);
        return $this->json([
            'info' => 'E-mail with instructions to reset password has been sent.'
        ]);
    }

    /**
     * @Route("/confirm-password-reset/{token}", name="security_confirm_password_reset", methods="GET")
     */
    public function completeResetPassword(string $token, ResetPasswordService $resetPasswordService)
    {
        $resetPasswordService->completeResetPassword($token);
        return $this->json([
            'info' => 'Password has been reset successfully. Check your email for new password'
        ]);
    }

    /**
     * @Route("/register", name="security_register", methods="POST")
     */
    public function initRegistration(Request $request, RegisterService $registerService)
    {
        $registerService->initRegistration($request);
        return $this->json([
            'info' => 'Success. Check your e-mail and confirm your account to complete registration'
        ]);
    }

    /**
     * @Route("/register/confirm/{token}", name="security_confirm_registration", methods="GET")
     */
    public function completeRegistration(string $token, RegisterService $registerService)
    {
        $registerService->completeRegistration($token);
        return $this->json([
            'info' => 'Account registration completed. You can now log in.'
        ]);
    }

    /**
     * @Route("/users/{id}/change-role", name="security_change_role", methods="PATCH")
     */
    public function changeRole(Request $request, int $id, ChangeRoleService $changeRoleService)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $changeRoleService->changeRole($request, $id);
        return $this->json(['info' => 'Role has been set']);
    }

    /**
     * @Route("/change-email", name="security_change_email", methods="PATCH")
     */
    public function changeEmail(Request $request, ChangeEmailService $changeEmailService)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $changeEmailService->changeEmail($request, $this->getUser());
        return $this->json(['info' => 'Email address has been set']);
    }


}