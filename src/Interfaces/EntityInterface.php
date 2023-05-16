<?php

declare(strict_types=1);

namespace Core\Entify\Interfaces;

use Core\Entify\Interfaces\EntityHandlersInterface;

/**
 * Main entity interface. You can get it from DataProviderInterface object.
 * Entity can be rendered by RendererInterface or from entity you can
 * get data array
 */
interface EntityInterface
{

    /**
     * Export this entity to array
     * @param bool $first Only first record
     * @return array|null
     */
    public function export(): array|null;

    /**
     * Export only first of result
     * @param int $index Index element in array
     * @return array|null
     */
    public function exportOne(int $index = 0): array|null;

    /**
     * Render repository entity with RenderRepoInterface
     * @param RenderRepoInterface $renderer
     * @return mixed
     */
    public function renderRepo(RenderRepoInterface $renderer): mixed;

    /**
     * Render single entity with RenderSingleInterface
     * @param RenderSingleInterface $renderer
     * @param int $index
     * @return mixed
     */
    public function renderOne(RenderSingleInterface $renderer, int $index = 0): mixed;

    /**
     * Refresh data. This is not querying data from DataProvider
     * This is repeating processes in EntityMain
     * @return self
     */
    public function refresh(): self;
    
    /**
     * Get entity info if exists. For example it can be pagination info
     * @return array|null
     */
    public function getInfo(): array|null;

    /**
     * Get handlers storage
     * @return EntityHandlersInterface
     */
    public function getHandlers(): EntityHandlersInterface;

    /**
     * Get error list array
     * @return array|null
     */
    public function getErrors(): array|null;
}
