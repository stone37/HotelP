<?php

namespace App\Repository;

use App\Entity\Promotion;
use App\Entity\Room;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Promotion>
 *
 * @method Promotion|null find($id, $lockMode = null, $lockVersion = null)
 * @method Promotion|null findOneBy(array $criteria, array $orderBy = null)
 * @method Promotion[]    findAll()
 * @method Promotion[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PromotionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Promotion::class);
    }

    public function add(Promotion $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Promotion $entity, bool $flush = false): void
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

    public function getEnabled(): array
    {
        $qb = $this->createQueryBuilder('p')
            ->leftJoin('p.room', 'room')
            ->addSelect('room')
            ->where('p.enabled = 1')
            ->andWhere('p.start <= :start')
            ->andWhere('p.end >= :end')
            ->orderBy('p.position', 'asc')
            ->setParameter('start', new DateTime())
            ->setParameter('end', new DateTime());

        return $qb->getQuery()->getResult();
    }

    public function getTotalEnabled(): array
    {
        $qb = $this->createQueryBuilder('p')
            ->leftJoin('p.room', 'room')
            ->addSelect('room')
            ->where('p.enabled = 1')
            ->andWhere('p.end >= :end')
            ->setParameter('end', new DateTime())
            ->orderBy('p.position', 'asc');

        return $qb->getQuery()->getResult();
    }

    /**
     * @throws NonUniqueResultException
     */
    public function getBySlug(string $slug): ?Promotion
    {
        $qb = $this->createQueryBuilder('p')
            ->leftJoin('p.room', 'room')
            ->addSelect('room')
            ->where('p.enabled = 1')
            ->andWhere('p.slug = :slug')
            ->setParameter('slug', $slug);

        return $qb->getQuery()->getOneOrNullResult();
    }

    public function findLimit(int $limit): array
    {
        $qb = $this->createQueryBuilder('p')
            ->leftJoin('p.room', 'room')
            ->addSelect('room')
            ->where('p.enabled = 1')
            ->andWhere('p.start <= :start')
            ->andWhere('p.end >= :end')
            ->orderBy('p.position', 'asc')
            ->setParameter('start', new DateTime())
            ->setParameter('end', new DateTime())
            ->setMaxResults($limit);

        return $qb->getQuery()->getResult();
    }

    public function getByRoom(Room $room): int
    {
        $qb = $this->createQueryBuilder('p');

        $query = $qb->select('p.discount')
            ->where('p.enabled = 1')
            ->andWhere('p.room = :room')
            ->andWhere('p.start <= :start')
            ->andWhere('p.end >= :end')
            ->orderBy('p.createdAt', 'desc')
            ->setParameter('room', $room)
            ->setParameter('start', new DateTime())
            ->setParameter('end', new DateTime())
            ->setMaxResults(1)
            ->getQuery();

        if ($query->getOneOrNullResult() !== null){
            return $query->getSingleScalarResult();
        }

        return 0;
    }

}
