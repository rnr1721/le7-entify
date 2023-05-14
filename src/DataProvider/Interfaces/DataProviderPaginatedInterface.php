<?php

namespace Core\Entify\DataProvider\Interfaces;

use Core\Entify\Interfaces\DataProviderInterface;

/**
 * Data providers for entities that can be paginated
 * Pagination info must get into $info in EntityInterface
 */
interface DataProviderPaginatedInterface extends DataProviderInterface
{

    /**
     * Paginate result
     * @param int $perPage
     * @param int $page
     * @param int $prevCount
     * @param int $nextCount
     * @return void
     */
    public function paginate(
            int $perPage = 15,
            int $page = 1,
            int $prevCount = 5,
            int $nextCount = 5
    ): void;
}
