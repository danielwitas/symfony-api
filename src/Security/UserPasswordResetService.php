<?php

namespace App\Security;

use App\Api\ApiProblem;
use App\Entity\User;
use App\Exception\ApiProblemException;
use App\Mailer\Mailer;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
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
            $apiProblem = new ApiProblem(
                Response::HTTP_BAD_REQUEST,
                ApiProblem::TYPE_INVALID_CONFIRMATION_TOKEN
            );
            $apiProblem->set('details', 'Invalid confirmation token. Check your token and try again.');
            throw new ApiProblemException($apiProblem);
        }
        $password = $this->tokenGenerator->getRandomSecureToken();
        $user->setPassword(
            $this->userPasswordEncoder->encodePassword($user, $password)
        );
        $user->setEnabled(true);
        $user->setConfirmationToken(null);
        $this->entityManager->flush();
        $this->mailer->sendNewPassword($user, $password);
        return ['info' => 'Password has been reset successfully'];
    }
}