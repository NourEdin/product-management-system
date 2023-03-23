<?php

namespace App\Repository;

use App\Entity\Pack;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Pack>
 *
 * @method Pack|null find($id, $lockMode = null, $lockVersion = null)
 * @method Pack|null findOneBy(array $criteria, array $orderBy = null)
 * @method Pack[]    findAll()
 * @method Pack[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PackRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Pack::class);
    }

    public function save(Pack $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function softDelete(Pack $entity, bool $flush = false): void
    {
        // Soft delete
        $entity->setDeleted(true);
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
    /**
     * Gets all packs except deleted ones
     * @param array $options Query options
     *      @type $options['offset']    The row to start from
     *      @type $options['max']       The max number of rows to fetch in the same page
     *      @type $options['term']      The string to search by
     *      @type $options['sort']      The field to order by
     *      @type $options['order']     Order direction    
     */
    public function findAllExceptDeleted($options = []) {
        $sort = 'id'; //The default field to order by
        $order = 'asc'; //Default direction

        if (isset($options['sort'])) {
            //Supported sorting fields:
            $supported = ['name', 'id', 'created_at', 'updated_at'];
            if (in_array($options['sort'], $supported))
                $sort = $options['sort'];
        }

        if (isset($options['order'])) {
            if (in_array($options['order'], ['asc', 'desc']))
                $order = $options['order'];
        }
        
        $builder = $this->createQueryBuilder('p')
            ->andWhere('p.deleted is null OR p.deleted = 0')
            ->orderBy("p.$sort", $order);

        if (isset($options['term'])) {
            $builder->andWhere("p.name like :term")
            ->setParameter('term', "%{$options['term']}%");
        }
        if (isset($options['max']) && $options['max'] > 0) {
            $builder->setMaxResults($options['max']);
        }
        if (isset($options['offset']) && $options['offset'] > 0) {
            $builder->setFirstResult($options['offset']);
        }
           
        return $builder
        ->getQuery()
        ->getResult();
    }
    /**
     * Finds a pack by its id and returns null if it's deleted
     */
    public function findOneExceptDeleted($id) {
        $builder = $this->createQueryBuilder('p')
        ->andWhere('p.id = :id')
        ->setParameter('id', $id)
        ->andWhere('p.deleted is null OR p.deleted = 0');
        
           
        $result = $builder
        ->getQuery()
        ->getResult();

        if (count($result)) return $result[0];
        return;
    }
    /**
     * Gets total number of filtered packs except deleted ones
     * @param array $options Query options
     *      @type $options['term']      The string to search by
     */
    public function getTotalExceptDeleted($options) {
        $builder = $this->createQueryBuilder('p')
        ->select('count(p.id)')
        ->andWhere('p.deleted is null OR p.deleted = 0');

        if (isset($options['term']) && trim($options['term'])) {
            $builder->andWhere("p.name like :term")
            ->setParameter('term', "%{$options['term']}%");
        }
        
        return $builder
        ->getQuery()
        ->getSingleScalarResult();
    }

//    /**
//     * @return Pack[] Returns an array of Pack objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Pack
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
