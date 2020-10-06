<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function findAllQueryBuilder($user, $filter = '')
    {
        if(!$filter) {
            return $this->findAllByOwner($user);
        }
        return $this->findAllByFilter($user, $filter);
    }

    public function findAllByFilter($user, $filter)
    {
        $qb = $this->createQueryBuilder('product');
        $qb->andWhere('product.name LIKE :filter')
            ->andWhere('product.owner = :owner')
            ->andWhere('product.template IS NULL')
            ->orderBy('product.id', 'DESC')
            ->setParameters([
                'filter' => '%'.$filter.'%',
                'owner' => $user,
            ]);
        return $qb;
    }

    public function findAllByOwner($user)
    {
        $qb = $this->createQueryBuilder('product');
        if($user) {
            $qb->andWhere('product.owner = :owner')
                ->andWhere('product.template IS NULL')
                ->orderBy('product.id', 'DESC')
                ->setParameters([
                    'owner' => $user,
                ]);
        }
        return $qb;

    }

    // /**
    //  * @return Product[] Returns an array of Product objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Product
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
