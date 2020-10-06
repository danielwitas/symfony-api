<?php


namespace App\Security;


use App\Entity\User;
use App\Form\ChangeRoleType;
use App\Validation\ApiValidator;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ChangeRoleService
{

    private $entityManager;
    private $container;
    private $apiValidator;

    public function __construct(
        EntityManagerInterface $entityManager,
        ContainerInterface $container,
        ApiValidator $apiValidator
    )
    {
        $this->entityManager = $entityManager;
        $this->container = $container;
        $this->apiValidator = $apiValidator;
    }

    public function changeRole(Request $request, int $id)
    {
        $user = $this->fetchUserById($id);
        $form = $this->createForm(ChangeRoleType::class, $user);
        $this->apiValidator->processForm($request, $form);
        $this->entityManager->flush();
    }

    private function createForm(string $type, $data = null, array $options = []): FormInterface
    {
        return $this->container->get('form.factory')->create($type, $data, $options);
    }

    private function fetchUserById(int $id)
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['id' => $id]);
        if (!$user) {
            throw new NotFoundHttpException(sprintf('Could not find user with id %d', $id));
        }
        return $user;
    }
}