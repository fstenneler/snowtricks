<?php

namespace App\Repository;

use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Category|null find($id, $lockMode = null, $lockVersion = null)
 * @method Category|null findOneBy(array $criteria, array $orderBy = null)
 * @method Category[]    findAll()
 * @method Category[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }

    /**
     * Find one category by slug
     *
     * @param $categorySlug
     * @return Collection
     */
    public function findOneBySlug($categorySlug)
    {
        return $this->createQueryBuilder('c')
            ->where('c.slug = :cat')
            ->setParameter('cat', $categorySlug)
            ->getQuery()
            ->getResult();
    }

}
