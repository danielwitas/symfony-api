<?php

namespace App\Security;

use App\Api\ApiProblem;
use App\Entity\User;
use App\Exception\ApiProblemException;
use App\Form\PasswordResetType;
use App\Mailer\Mailer;
use App\Validation\ApiValidator;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ResetPasswordService
{
    private $entityManager;
    private $container;
    private $apiValidator;
    private $tokenGenerator;
    private $mailer;
    private $userPasswordEncoder;

    public function __construct(
        EntityManagerInterface $entityManager,
        ContainerInterface $container,
        ApiValidator $apiValidator,
        TokenGenerator $tokenGenerator,
        Mailer $mailer,
        UserPasswordEncoderInterface $userPasswordEncoder
    )
    {
        $this->entityManager = $entityManager;
        $this->container = $container;
        $this->apiValidator = $apiValidator;
        $this->tokenGenerator = $tokenGenerator;
        $this->mailer = $mailer;
        $this->userPasswordEncoder = $userPasswordEncoder;
    }

    public function initResetPassword(Request $request)
    {
        $form = $this->createForm(PasswordResetType::class, new User());
        $data = $this->apiValidator->processForm($request, $form);
        $user = $this->fetchUserByEmail($data->getEmail());
        /** @var User $user */
        $user->setConfirmationToken($this->tokenGenerator->getRandomSecureToken());
        $this->entityManager->flush();
        $this->mailer->sendPasswordResetEmail($user);
    }

    public function completeResetPassword(string $confirmationToken)
    {
        $user = $this->fetchUserByConfirmationToken($confirmationToken);
        $password = $this->tokenGenerator->getRandomSecureToken();
        $user->setPassword(
            $this->userPasswordEncoder->encodePassword($user, $password)
        );
        $user->setEnabled(true);
        $user->setConfirmationToken(null);
        $this->entityManager->flush();
        $this->mailer->sendNewPassword($user, $password);
    }


    private function createForm(string $type, $data = null, array $options = []): FormInterface
    {
        return $this->container->get('form.factory')->create($type, $data, $options);
    }

    private function fetchUserByEmail(string $email)
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
        if (!$user) {
            throw new NotFoundHttpException(sprintf('Could not find user with e-mail %s', $email));
        }
        return $user;
    }

    private function fetchUserByConfirmationToken(string $confirmationToken)
    {
        /** @var User $user */
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['confirmationToken' => $confirmationToken]);
        if (!$user) {
            $apiProblem = new ApiProblem(
                Response::HTTP_BAD_REQUEST,
                ApiProblem::TYPE_INVALID_CONFIRMATION_TOKEN
            );
            $apiProblem->set('details', 'Invalid confirmation token. Check your token and try again.');
            throw new ApiProblemException($apiProblem);
        }
        return $user;
    }
}