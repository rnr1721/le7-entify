<?php

namespace Core\Entify\DataProvider\Interfaces;

use Core\Entify\Interfaces\DataProviderInterface;

interface DataProviderPaginatedInterface extends DataProviderInterface
{

    public function paginate(int $perPage = 15, int $page = 1): void;
}
