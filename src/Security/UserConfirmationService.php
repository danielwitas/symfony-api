<?php


namespace App\Security;

use App\Api\ApiProblem;
use App\Entity\User;
use App\Exception\ApiProblemException;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;

class UserConfirmationService
{
    private $userRepository;
    private $entityManager;

    public function __construct(UserRepository $userRepository, EntityManagerInterface $entityManager)
    {
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
    }

    public function confirmUser(string $confirmationToken)
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
        $user->setEnabled(true);
        $user->setConfirmationToken(null);
        $this->entityManager->flush();
        return ['info' => 'Account registration completed. You can now log in.'];
    }
}