<?php

namespace Core\Entify;

use Core\Entify\Interfaces\PaginatorInterface;
use function ceil,
             max,
             min,
             range;

/**
 * Pagination class
 */
class Paginator implements PaginatorInterface
{

    private int $page;
    private int $perPage;
    private int $totalCount;
    private int $lastPage;
    private int $offset;

    public function __construct(int $page, int $perPage, int $totalCount)
    {
        $this->page = $page;
        $this->perPage = $perPage;
        $this->totalCount = $totalCount;
        $this->lastPage = (int) ceil($totalCount / $perPage);
        $this->offset = ($this->page - 1) * $this->perPage;
    }

    /**
     * @inheritdoc
     */
    public function getPage(): int
    {
        return $this->page;
    }

    /**
     * @inheritdoc
     */
    public function getPerPage(): int
    {
        return $this->perPage;
    }

    /**
     * @inheritdoc
     */
    public function getTotalCount(): int
    {
        return $this->totalCount;
    }

    /**
     * @inheritdoc
     */
    public function getLastPage(): int
    {
        return $this->lastPage;
    }

    /**
     * @inheritdoc
     */
    public function getOffset(): int
    {
        return $this->offset;
    }

    /**
     * Array of previous page numbers
     * @param int $count Pages count to display numbers
     * @return array
     */
    protected function getPreviousPages(int $count = 5): array
    {
        $start = max(1, $this->page - $count);
        $end = $this->page - 1;

        return range($start, $end >= $start ? $end : $start - 1);
    }

    /**
     * Array of next pages numbers
     * @param int $count
     * @return array
     */
    protected function getNextPages(int $count = 5): array
    {
        $start = $this->page + 1;
        $end = min($this->page + $count, $this->lastPage);

        return range($start <= $end ? $start : $this->lastPage, $end);
    }

    /**
     * @inheritdoc
     */
    public function toArray(
            int $currentCount,
            int $prevCount = 5,
            int $nextCount = 5
    ): array
    {
        return [
            'current_page' => $this->page,
            'per_page' => $this->perPage,
            'total' => $this->totalCount,
            'last_page' => $this->lastPage,
            'from' => $this->offset + 1,
            'to' => $this->offset + $currentCount,
            'previous_pages' => $this->getPreviousPages($prevCount),
            'next_pages' => $this->getNextPages($nextCount),
        ];
    }

}
