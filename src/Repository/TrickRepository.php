<?php

namespace App\Repository;

use App\Entity\Trick;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Trick|null find($id, $lockMode = null, $lockVersion = null)
 * @method Trick|null findOneBy(array $criteria, array $orderBy = null)
 * @method Trick[]    findAll()
 * @method Trick[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TrickRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Trick::class);
    }

    public function findTricks($parameters = [])
    {

        // set default parameters
        $request = array(
            'categoryId' => 0,
            'firstResult' => 1,
            'maxResults' => Trick::NUM_ITEMS,
            'orderBy' => 'creationDate-DESC'
        );

        // replace default parameters by query parameters
        foreach($parameters as $key => $val) {
            $request[$key] = $val;
        }

        // verify and format orderBy parameter
        if(!in_array($request['orderBy'], ['creationDate-ASC', 'creationDate-DESC', 'name-ASC', 'name-DESC'])) {
            $request['orderBy'] = 'creationDate-DESC';
        }        
        $request['orderByField'] = preg_replace("#-(.+)$#","",$request['orderBy']);
        $request['orderByDirection'] = preg_replace("#^(.+)-#","",$request['orderBy']);

        // do query
        $qb = $this->createQueryBuilder('t')
            ->addSelect('m')
            ->leftJoin('t.media', 'm', 'WITH', 'm.isHeader = 1')
            ->orderBy('t.' . $request['orderByField'], $request['orderByDirection'])
            ->setFirstResult($request['firstResult'])
            ->setMaxResults($request['maxResults']);
        
        if($request['categoryId'] > 0) {
            $qb->andWhere('t.category = :cat')
                ->setParameter('cat', $request['categoryId']);
        }

        return new Paginator($qb);

    }

    public function findCategories()
    {
        return $this->createQueryBuilder('t')
            ->addSelect('c')
            ->innerJoin('t.category', 'c') 
            ->groupBy('t.category')
            ->orderBy('c.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

}
