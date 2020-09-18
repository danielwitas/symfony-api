<?php


namespace App\Controller;

use App\Api\ApiProblem;
use App\Entity\User;
use App\Exception\ApiProblemException;
use App\Form\ChangeEmailType;
use App\Form\ChangePasswordType;
use App\Form\ChangeRoleType;
use App\Form\PasswordResetType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SecurityController extends ApiController
{
    /**
     * @Route("/login", name="security_login", methods={"POST"})
     */
    public function login()
    {
        if (!$this->isGranted('IS_AUTHENTICATED_FULLY')) {
            $apiProblem = new ApiProblem(
                Response::HTTP_BAD_REQUEST,
                ApiProblem::TYPE_INVALID_LOGIN_REQUEST
            );
            throw new ApiProblemException($apiProblem);
        }
        return $this->createApiResponse(['info' => 'You have logged in successfully.']);
    }

    /**
     * @Route("/logout", name="security_logout")
     */
    public function logout()
    {

    }

    /**
     * @Route("/change-password", name="security_change_password", methods="PATCH")
     */
    public function changePassword(Request $request)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->entityManager->getRepository(User::class)->findOneBy(
            ['username' => $this->getUser()->getUsername()]
        );
        $data = $this->jsonDecode($request->getContent());
        $this->isJsonValid($data);
        $form = $this->createForm(ChangePasswordType::class, new User());
        $form->submit($data, false);
        $this->isFormValid($form);
        $checkPass = $this->userPasswordEncoder->isPasswordValid($this->getUser(), $form->getData()->getPassword());
        if (!$checkPass) {
            $apiProblem = new ApiProblem(
                Response::HTTP_BAD_REQUEST,
                ApiProblem::TYPE_INVALID_CREDENTIALS
            );
            throw new ApiProblemException($apiProblem);
        }
        /** @var User $user */
        $user->setPassword(
            $this->userPasswordEncoder->encodePassword($user, $form->getData()->getNewPassword())
        );
        $user->setPasswordChangeDate(time());
        $this->entityManager->persist($user);
        $this->entityManager->flush();
        return $this->createApiResponse(['info' => 'Password has been changed.']);
    }

    /**
     * @Route("/password-reset", name="security_password_reset", methods="POST")
     */
    public function passwordReset(Request $request)
    {
        $data = $this->jsonDecode($request->getContent());
        $this->isJsonValid($data);
        $form = $this->createForm(PasswordResetType::class, new User());
        $form->submit($data);
        $this->isFormValid($form);
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $form->getData()->getEmail()]);
        if (!$user) {
            throw $this->createNotFoundException(sprintf('Could not find user with e-mail %s', $form->getData()->getEmail()));
        }
        $user->setConfirmationToken($this->tokenGenerator->getRandomSecureToken());
        $this->entityManager->flush();
        $this->mailer->sendPasswordResetEmail($user);
        return $this->createApiResponse(['info' => 'E-mail with instructions to reset password has been sent.']);
    }

    /**
     * @Route("/confirm-registration/{token}", name="security_confirm_registration", methods="GET")
     */
    public function confirmRegistration(string $token)
    {
        $data = $this->userConfirmationService->confirmUser($token);
        return $this->createApiResponse($data);
    }

    /**
     * @Route("/confirm-password-reset/{token}", name="security_confirm_password_reset", methods="GET")
     */
    public function confirmPasswordReset(string $token)
    {
        $data = $this->userPasswordResetService->resetPassword($token);
        return $this->createApiResponse($data);
    }

    /**
     * @Route("/users/{id}/change-role", name="security_change_role", methods="PATCH")
     */
    public function changeRole(Request $request, int $id)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['id' => $id]);
        if (!$user) {
            throw $this->createNotFoundException(sprintf('Could not find user with id %d', $id));
        }
        $data = $this->jsonDecode($request->getContent());
        $this->isJsonValid($data);
        $form = $this->createForm(ChangeRoleType::class, $user);
        $form->submit($data);
        $this->isFormValid($form);
        $this->entityManager->flush();
        return $this->createApiResponse(['info' => 'Role has been set']);
    }

    /**
     * @Route("change-email", name="security_change_email", methods="PATCH")
     */
    public function changeEmail(Request $request)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->entityManager->getRepository(User::class)->findOneBy(
            ['username' => $this->getUser()->getUsername()]);
        $data = $this->jsonDecode($request->getContent());
        $this->isJsonValid($data);
        $form = $this->createForm(ChangeEmailType::class, $user);
        $form->submit($data);
        $this->isFormValid($form);
        $this->entityManager->flush();
        return $this->createApiResponse(['info' => 'Email address has been set']);

        // add form
        // add voter
        return $this->json(['tba' => 'tba']);
    }


}