<?php

namespace App\Repository;

use App\Entity\Template;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Template|null find($id, $lockMode = null, $lockVersion = null)
 * @method Template|null findOneBy(array $criteria, array $orderBy = null)
 * @method Template[]    findAll()
 * @method Template[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TemplateRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Template::class);
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
        $qb = $this->createQueryBuilder('template');
        $qb->andWhere('template.name LIKE :filter')
            ->andWhere('template.owner = :owner')
            ->orderBy('template.id', 'DESC')
            ->setParameters([
                'filter' => '%'.$filter.'%',
                'owner' => $user
            ]);
        return $qb;
    }

    public function findAllByOwner($user)
    {
        $qb = $this->createQueryBuilder('template');
        if($user) {
            $qb->andWhere('template.owner = :owner')
                ->orderBy('template.id', 'DESC')
                ->setParameter('owner', $user);
        }
        return $qb;

    }

    // /**
    //  * @return Template[] Returns an array of Template objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Template
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
