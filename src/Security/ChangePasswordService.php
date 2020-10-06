<?php


namespace App\Security;


use App\Api\ApiProblem;
use App\Entity\User;
use App\Exception\ApiProblemException;
use App\Form\ChangePasswordType;
use App\Validation\ApiValidator;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class ChangePasswordService
{
    private $entityManager;
    private $container;
    private $userPasswordEncoder;
    private $apiValidator;

    public function __construct(
        EntityManagerInterface $entityManager,
        ContainerInterface $container,
        UserPasswordEncoderInterface $userPasswordEncoder,
        ApiValidator $apiValidator
    )
    {

        $this->entityManager = $entityManager;
        $this->container = $container;
        $this->userPasswordEncoder = $userPasswordEncoder;
        $this->apiValidator = $apiValidator;
    }

    public function changePassword(Request $request, UserInterface $user)
    {
        /** @var UserInterface $dbUser */
        $dbUser = $this->fetchUserByUsername($user);
        $form = $this->createForm(ChangePasswordType::class, new User());
        $formUser = $this->apiValidator->processForm($request, $form);
        $this->checkPassword($dbUser, $formUser);
        $this->setNewPassword($dbUser, $formUser);
    }

    private function createForm(string $type, $data = null, array $options = []): FormInterface
    {
        return $this->container->get('form.factory')->create($type, $data, $options);
    }

    private function fetchUserByUsername(UserInterface $user)
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy(
            ['username' => $user->getUsername()]
        );
        if(!$user) {
            throw new NotFoundHttpException('User does not exist');
        }
        return $user;
    }

    private function checkPassword(UserInterface $dbUser, UserInterface $formUser)
    {
        $checkPass = $this->userPasswordEncoder->isPasswordValid($dbUser, $formUser->getPassword());
        if (!$checkPass) {
            $apiProblem = new ApiProblem(
                Response::HTTP_BAD_REQUEST,
                ApiProblem::TYPE_INVALID_CREDENTIALS
            );
            throw new ApiProblemException($apiProblem);
        }
    }

    private function setNewPassword(UserInterface $dbUser, UserInterface $formUser)
    {
        $dbUser->setPassword(
            $this->userPasswordEncoder->encodePassword($dbUser, $formUser->getNewPassword())
        );
        $dbUser->setPasswordChangeDate(time());
        $this->entityManager->persist($dbUser);
        $this->entityManager->flush();
    }

}