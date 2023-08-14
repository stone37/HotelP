<?php

namespace App\Paginator;

use Doctrine\ORM\Query;
use Knp\Component\Pager\Pagination\PaginationInterface;

interface PaginatorInterface
{
    public function allowSort(string ...$fields): self;

    public function paginate(Query $query): PaginationInterface;
}