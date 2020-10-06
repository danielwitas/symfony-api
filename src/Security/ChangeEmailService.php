<?php


namespace App\Security;


use App\Api\ApiProblem;
use App\Entity\User;
use App\Exception\ApiProblemException;
use App\Form\ChangeEmailType;
use App\Validation\ApiValidator;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;

class ChangeEmailService
{
    private $entityManager;
    private $container;
    private $apiValidator;

    public function __construct(EntityManagerInterface $entityManager, ContainerInterface $container, ApiValidator $apiValidator)
    {
        $this->entityManager = $entityManager;
        $this->container = $container;
        $this->apiValidator = $apiValidator;
    }
    public function changeEmail(Request $request, UserInterface $user)
    {
        $form = $this->createForm(ChangeEmailType::class, $user);
        $email = $this->apiValidator->processForm($request, $form);
        $this->isEmailTaken($email);
        $this->entityManager->flush();
    }

    private function createForm(string $type, $data = null, array $options = []): FormInterface
    {
        return $this->container->get('form.factory')->create($type, $data, $options);
    }

    private function isEmailTaken(string $email)
    {
        $result = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
        if($result) {
            $apiProblem = new ApiProblem(
                Response::HTTP_BAD_REQUEST,
                ApiProblem::TYPE_EMAIL_ALREADY_EXISTS
            );
            throw new ApiProblemException($apiProblem);
        }
    }


}