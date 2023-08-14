<?php

namespace App\Paginator;

use App\Exception\PageOutOfBoundException;
use Doctrine\ORM\Query;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class KnpPaginator implements PaginatorInterface
{
    private array $sortableFields = [];

    public function __construct(
        private \Knp\Component\Pager\PaginatorInterface $paginator,
        private RequestStack $requestStack
    )
    {
    }

    public function allowSort(string ...$fields): PaginatorInterface
    {
        $this->sortableFields = array_merge($this->sortableFields, $fields);

        return $this;
    }

    public function paginate(Query $query): PaginationInterface
    {
        $request = $this->requestStack->getCurrentRequest();
        $page = $request ? $request->query->getInt('page', 1) : 1;

        if ($page <= 0) {
            throw new PageOutOfBoundException();
        }

        return $this->paginator->paginate($query, $page, $query->getMaxResults() ?: 15, [
            'sortFieldWhitelist' => $this->sortableFields,
            'filterFieldWhitelist' => []
        ]);
    }
}
