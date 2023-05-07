<?php

declare(strict_types=1);

namespace Core\Entify\DataProvider;

use Core\Entify\Interfaces\EntityHandlersInterface;
use Core\Entify\DataProvider\Interfaces\DataProviderPaginatedInterface;
use Core\Entify\Interfaces\EntityInterface;
use Core\Entify\EntityMain;
use function count,
             ceil,
             max,
             min,
             array_slice;

/**
 * Data provider for arrays
 */
class ArrayDataProvider implements DataProviderPaginatedInterface
{

    /**
     * Entity handlers object for manage list of handlers
     * that will be run in EntityInterface
     * @var EntityHandlersInterface
     */
    private EntityHandlersInterface $entityHandlers;
    
    /**
     * Array data, that need to be processed
     * @var array
     */
    private array $data;
    
    /**
     * Paginated array after pagination
     * @var array|null
     */
    private ?array $paginatedData = null;
    
    /**
     * Additional info about pagination or other
     * @var array|null
     */
    private ?array $info = null;

    public function __construct(
            EntityHandlersInterface $entityHandlers,
            array $data
    )
    {
        $this->entityHandlers = $entityHandlers;
        $this->data = $data;
    }

    /**
     * @inheritDoc
     */
    public function paginate(int $perPage = 15, int $page = 1): void
    {
        $total = count($this->data);
        $lastPage = (int) ceil($total / $perPage);
        $currentPage = max(1, min($lastPage, $page));

        $offset = ($currentPage - 1) * $perPage;
        $sliced = array_slice($this->data, $offset, $perPage, true);

        $this->info = [
            'current_page' => $currentPage,
            'from' => $offset + 1,
            'last_page' => $lastPage,
            'per_page' => $perPage,
            'to' => $offset + count($sliced),
            'total' => $total,
        ];

        $this->paginatedData = $sliced;
    }

    /**
     * @inheritDoc
     */
    public function getEntity(): EntityInterface
    {

        $data = $this->paginatedData ?? $this->data;

        return new EntityMain(
                $this->entityHandlers,
                $data,
                $this->info
        );
    }

}
