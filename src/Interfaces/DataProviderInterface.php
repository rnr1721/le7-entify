<?php

declare(strict_types=1);

namespace Core\Entify\Interfaces;

use Core\Entify\Interfaces\EntityOptionsInterface;
use Core\Entify\Interfaces\EntityInterface;

/**
 * Interface for DataProvider objects. DataProvider is class that from
 * any source get data, convert it to array that can be handled by
 * EntityInterface. The DataProviderInterface have getEntity() method,
 * that returns EntityInterface, that can be rendered
 */
interface DataProviderInterface
{

    /**
     * Get options or Entity processing for change it
     * @return EntityOptionsInterface
     */
    public function getOptions(): EntityOptionsInterface;

    /**
     * Get ready entity that can be rendered by EntityRenderer interface
     * @return EntityInterface
     */
    public function getEntity(): EntityInterface;
}
