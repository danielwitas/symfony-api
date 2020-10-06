<?php


namespace App\Security;


use App\Api\ApiProblem;
use App\Entity\User;
use App\Exception\ApiProblemException;
use App\Form\UserType;
use App\Mailer\Mailer;
use App\Validation\ApiValidator;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegisterService
{
    private $container;
    private $apiValidator;
    private $tokenGenerator;
    private $userPasswordEncoder;
    private $entityManager;
    private $mailer;

    public function __construct(
        ContainerInterface $container,
        ApiValidator $apiValidator,
        TokenGenerator $tokenGenerator,
        UserPasswordEncoderInterface $userPasswordEncoder,
        EntityManagerInterface $entityManager,
        Mailer $mailer
    )
    {
        $this->container = $container;
        $this->apiValidator = $apiValidator;
        $this->tokenGenerator = $tokenGenerator;
        $this->userPasswordEncoder = $userPasswordEncoder;
        $this->entityManager = $entityManager;
        $this->mailer = $mailer;
    }

    public function initRegistration(Request $request)
    {
        $form = $this->createForm(UserType::class, new User(), ['validation_groups' => ['registration']]);
        $user = $this->apiValidator->processForm($request, $form);
        $this->persist($user);
    }

    public function completeRegistration(string $confirmationToken)
    {
        /** @var User $user */
        $user = $this->fetchUserByConfirmationToken($confirmationToken);
        $user->setEnabled(true);
        $user->setConfirmationToken(null);
        $this->entityManager->flush();
    }

    private function persist(User $user)
    {
        $user->setConfirmationToken($this->tokenGenerator->getRandomSecureToken());
        $user->setEnabled(false);
        $user->setPassword(
            $this->userPasswordEncoder->encodePassword($user, $user->getPassword())
        );
        $this->entityManager->persist($user);
        $this->entityManager->flush();
        $this->mailer->sendConfirmationEmail($user);
    }

    private function createForm(string $type, $data = null, array $options = []): FormInterface
    {
        return $this->container->get('form.factory')->create($type, $data, $options);
    }

    private function fetchUserByConfirmationToken(string $confirmationToken) {
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