<?php

namespace App\Repository;

use App\Entity\Comment;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Comment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Comment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Comment[]    findAll()
 * @method Comment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comment::class);
    }

    /**
     * Find comments using given query parameters
     *
     * @param array $parameters
     * @return Paginator
     */
    public function findComments($trickId, $firstResult)
    {

        // do query
        $qb = $this->createQueryBuilder('c')
            ->orderBy('c.creationDate', 'ASC')
            ->where('c.trick = :trick')
            ->setParameter('trick', $trickId)
            ->setFirstResult($firstResult)
            ->setMaxResults(Comment::NUM_ITEMS);

        return new Paginator($qb);

    }

}
