<?php

declare(strict_types=1);

namespace Core\Entify\DataProvider;

use Core\Entify\Interfaces\EntityInterface;
use Core\Entify\Interfaces\EntityHandlersInterface;
use Core\Entify\Interfaces\EntityOptionsInterface;
use Core\Entify\Paginator;
use Core\Entify\DataProvider\Interfaces\DataProviderPaginatedInterface;
use Core\Entify\EntityMain;
use function count,
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
    public function paginate(
            int $perPage = 15,
            int $page = 1,
            int $prevCount = 5,
            int $nextCount = 5
    ): self
    {
        $total = count($this->data);

        $paginator = new Paginator(
                $page,
                $perPage,
                $total);

        $offset = $paginator->getOffset();
        $sliced = array_slice($this->data, $offset, $perPage, true);

        $this->info = [
            'pagination' => $paginator->toArray(count($sliced), $prevCount, $nextCount)
        ];

        $this->paginatedData = $sliced;
        return $this;
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

    public function getOptions(): EntityOptionsInterface
    {
        return $this->entityHandlers->getOptions();
    }

}
