<?php

namespace App\Repository;

use App\Entity\EquipmentValue;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EquipmentValue>
 *
 * @method EquipmentValue|null find($id, $lockMode = null, $lockVersion = null)
 * @method EquipmentValue|null findOneBy(array $criteria, array $orderBy = null)
 * @method EquipmentValue[]    findAll()
 * @method EquipmentValue[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EquipmentValueRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EquipmentValue::class);
    }

    public function add(EquipmentValue $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(EquipmentValue $entity, bool $flush = false): void
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
}
