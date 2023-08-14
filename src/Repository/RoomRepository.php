<?php

namespace App\Repository;

use App\Entity\Room;
use App\Model\RoomFilter;
use App\Model\RoomSearch;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Room>
 *
 * @method Room|null find($id, $lockMode = null, $lockVersion = null)
 * @method Room|null findOneBy(array $criteria, array $orderBy = null)
 * @method Room[]    findAll()
 * @method Room[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RoomRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Room::class);
    }

    public function add(Room $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Room $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function flush(): void
    {
        $this->getEntityManager()->flush();
    }

    public function getAdmins(RoomSearch $search): ?QueryBuilder
    {
        $qb = $this->createQueryBuilder('r')
            ->leftJoin('r.bookings', 'bookings')
            ->leftJoin('r.equipments', 'equipments')
            ->leftJoin('r.galleries', 'galleries')
            ->leftJoin('r.supplements', 'supplements')
            ->leftJoin('r.promotions', 'promotions')
            ->leftJoin('r.taxe', 'taxe')
            ->addSelect('bookings')
            ->addSelect('equipments')
            ->addSelect('galleries')
            ->addSelect('supplements')
            ->addSelect('promotions')
            ->addSelect('taxe')
            ->orderBy('r.position', 'asc');

        if ($search->isEnabled()) {
            $qb->andWhere('r.enabled = 1');
        }

        if ($search->getName()) {
            $qb->andWhere('r.name LIKE :name')->setParameter('name', '%'.$search->getName().'%');
        }

        return $qb;
    }
    
    public function getEnabled(): array
    {
        $qb = $this->createQueryBuilder('r')
            ->leftJoin('r.bookings', 'bookings')
            ->leftJoin('r.equipments', 'equipments')
            ->leftJoin('r.galleries', 'galleries')
            ->leftJoin('r.supplements', 'supplements')
            ->leftJoin('r.promotions', 'promotions')
            ->leftJoin('r.taxe', 'taxe')
            ->addSelect('bookings')
            ->addSelect('equipments')
            ->addSelect('galleries')
            ->addSelect('supplements')
            ->addSelect('promotions')
            ->addSelect('taxe')
            ->andWhere('r.enabled = 1')
            ->orderBy('r.position', 'asc');

        return $qb->getQuery()->getResult();
    }

    public function getFilter(RoomFilter $filter): array
    {
        $occupant = $filter->adult + $filter->children;

        $qb = $this->createQueryBuilder('r')
            ->leftJoin('r.bookings', 'bookings')
            ->leftJoin('r.equipments', 'equipments')
            ->leftJoin('r.galleries', 'galleries')
            ->leftJoin('r.supplements', 'supplements')
            ->leftJoin('r.promotions', 'promotions')
            ->leftJoin('r.taxe', 'taxe')
            ->addSelect('bookings')
            ->addSelect('equipments')
            ->addSelect('galleries')
            ->addSelect('supplements')
            ->addSelect('promotions')
            ->addSelect('taxe')
            ->andWhere('r.enabled = 1')
            ->orderBy('r.position', 'asc');

        if ($filter->adult || $filter->children) {
            $qb->andWhere($qb->expr()->gte('r.occupant', ':occupant'))->setParameter('occupant', $occupant);
        }

        return $qb->getQuery()->getResult();
    }

    public function findRandom(int $limit): array
    {
        return $this->createQueryBuilder('r')
            ->leftJoin('r.bookings', 'bookings')
            ->leftJoin('r.equipments', 'equipments')
            ->leftJoin('r.galleries', 'galleries')
            ->leftJoin('r.supplements', 'supplements')
            ->leftJoin('r.promotions', 'promotions')
            ->leftJoin('r.taxe', 'taxe')
            ->addSelect('bookings')
            ->addSelect('equipments')
            ->addSelect('galleries')
            ->addSelect('supplements')
            ->addSelect('promotions')
            ->addSelect('taxe')
            ->where('r.enabled = 1')
            ->orderBy('r.position', 'asc')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * @throws NonUniqueResultException
     */
    public function getBySlug(string $slug): ?Room
    {
        $qb = $this->createQueryBuilder('r')
            ->leftJoin('r.bookings', 'bookings')
            ->leftJoin('r.equipments', 'equipments')
            ->leftJoin('r.galleries', 'galleries')
            ->leftJoin('r.supplements', 'supplements')
            ->leftJoin('r.promotions', 'promotions')
            ->leftJoin('r.taxe', 'taxe')
            ->addSelect('bookings')
            ->addSelect('equipments')
            ->addSelect('galleries')
            ->addSelect('supplements')
            ->addSelect('promotions')
            ->addSelect('taxe')
            ->where('r.slug = :slug')
            ->setParameter('slug', $slug);

        return $qb->getQuery()->getOneOrNullResult();
    }


    public function roomListeQueryBuilder(): QueryBuilder
    {
        $qb = $this->createQueryBuilder('r')
            ->orderBy('r.position', 'asc');

        return $qb;
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function getRoomTotalNumber(): ?int
    {
        $qb = $this->createQueryBuilder('r')
            ->select('SUM(r.roomNumber)');

        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function getRoomEnabledTotalNumber(): ?int
    {
        $qb = $this->createQueryBuilder('r')
            ->select('SUM(r.roomNumber)')
            ->where('r.enabled = 1');

        return $qb->getQuery()->getSingleScalarResult();
    }


    

}
