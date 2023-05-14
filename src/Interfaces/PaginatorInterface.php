<?php

namespace Core\Entify\Interfaces;

interface PaginatorInterface
{

    /**
     * Get current page number
     * @return int
     */
    public function getPage(): int;

    /**
     * Get records per page
     * @return int
     */
    public function getPerPage(): int;

    /**
     * Get total records
     * @return int
     */
    public function getTotalCount(): int;

    /**
     * Get last page number
     * @return int
     */
    public function getLastPage(): int;

    /**
     * Get offset
     * @return int
     */
    public function getOffset(): int;

    /**
     * Export to pagination array
     * @param int $currentCount Current count
     * @param int $prevCount Prevous pages count
     * @param int $nextCount Next pages count
     * @return array Pagination array info
     */
    public function toArray(
            int $currentCount,
            int $prevCount = 5,
            int $nextCount = 5
    ): array;
}
