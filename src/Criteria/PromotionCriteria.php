<?php

namespace App\Criteria;

use DateTime;
use Doctrine\ORM\QueryBuilder;
use App\Entity\Promotion;
use JetBrains\PhpStorm\Pure;

class PromotionCriteria
{
    public function filterQueryBuilder(QueryBuilder $queryBuilder): QueryBuilder
    {
        $root = $queryBuilder->getRootAliases()[0];

        $queryBuilder
            ->andWhere(sprintf('%s.startDate IS NULL OR %s.startDate <= :date', $root, $root))
            ->andWhere(sprintf('%s.endDate IS NULL OR %s.endDate > :date', $root, $root))
            ->andWhere($root.'.enabled = :enabled')
            ->setParameter('enabled', true)
            ->setParameter('date', new DateTime());

        return $queryBuilder;
    }

    #[Pure] public function verify(Promotion $promotion, DateTime $start, DateTime $end): bool
    {
        return
            ($promotion->getStart() === null || $promotion->getStart() <= $start) &&
            ($promotion->getEnd() === null || $promotion->getEnd() > $end) &&
            $promotion->isEnabled();
    }
}
