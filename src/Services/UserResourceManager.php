<?php


namespace App\Services;


use App\Entity\Product;
use App\Entity\Template;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;

class UserResourceManager extends ApiResourceManager
{
    public function getSingleUser(int $id)
    {
        return $this->fetchUserById($id);
    }

    public function getUserCollection(Request $request)
    {
        $filter = $request->query->get('filter');
        $qb = $this->entityManager->getRepository(User::class)->findAllQueryBuilder($filter);
        $paginatedCollection = $this->paginationFactory->createCollection($qb, $request, 'users_get_collection');
        return $paginatedCollection->getResult('users');
    }

    public function deleteUser(int $id)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $user = $this->fetchUserById($id);
        $this->entityManager->remove($user);
        $this->entityManager->flush();
    }

    public function getUserProducts(int $id)
    {
        $user = $this->fetchUserById($id);
        return $this->entityManager->getRepository(Product::class)->findBy(['owner' => $user]);
    }

    public function getUserTemplates(int $id)
    {
        $user = $this->fetchUserById($id);
        return $this->entityManager->getRepository(Template::class)->findBy(['owner' => $user]);
    }

    private function fetchUserById(int $id)
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['id' => $id]);
        if (!$user) {
            throw $this->createNotFoundException('This user does not exist');
        }
        return $user;
    }
}