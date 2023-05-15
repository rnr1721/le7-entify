<?php

declare(strict_types=1);

namespace Core\Entify\Interfaces;

/**
 * Interface for own handlers, that can be used with DefaultHandler.
 * User can plug own handlers, and queue it.
 */
interface EntityHandlerInterface
{

    /**
     * Handle data in entity
     * @param array $all List of entities
     * @param array|null $info Additional information
     * @return array|null
     */
    public function handle(array $all, array|null $info = null): array|null;

    /**
     * Get fresh info array.
     * Handler can change info
     * @return array
     */
    public function getInfo(): array;

    /**
     * Is need to refresh info?
     * When the handler chain is running, this flag determines whether the
     * $info in the EntityMain needs to be updated.
     * @return bool
     */
    public function isNeedRefreshInfo(): bool;

    /**
     * Get errors that you can get while processing entity data
     * @return array|null
     */
    public function getErrors(): array|null;
}
