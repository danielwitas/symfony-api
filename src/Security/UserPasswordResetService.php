<?php

namespace App\Security;

use App\Entity\User;
use App\Mailer\Mailer;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserPasswordResetService
{
    private $userRepository;
    private $entityManager;
    private $tokenGenerator;
    private $userPasswordEncoder;
    private $mailer;

    public function __construct(
        UserRepository $userRepository,
        EntityManagerInterface $entityManager,
        TokenGenerator $tokenGenerator,
        UserPasswordEncoderInterface $userPasswordEncoder,
        Mailer $mailer
    )
    {
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
        $this->tokenGenerator = $tokenGenerator;
        $this->userPasswordEncoder = $userPasswordEncoder;
        $this->mailer = $mailer;
    }

    public function resetPassword($confirmationToken)
    {
        /** @var User $user */
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['confirmationToken' => $confirmationToken]);
        if (!$user) {
            throw new NotFoundHttpException();
        }
        $password = $this->tokenGenerator->getRandomSecureToken();
        $user->setPassword(
            $this->userPasswordEncoder->encodePassword($user, $password)
        );
        $user->setEnabled(true);
        $user->setConfirmationToken(null);
        $this->entityManager->flush();
        $this->mailer->sendNewPassword($user, $password);
    }
}