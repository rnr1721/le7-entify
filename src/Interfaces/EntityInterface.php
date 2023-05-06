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
     * @return array|null
     */
    public function export(): array|null;

    /**
     * Render entity with EntityRendererInterface
     * @param EntityRendererInterface $renderer
     * @return mixed
     */
    public function render(EntityRendererInterface $renderer): mixed;

    /**
     * Get entity info if exists. For example it can be pagination info
     * @return array|null
     */
    public function getInfo(): array|null;

    /**
     * Get default handler configuration object
     * @return EntityOptionsInterface
     */
    public function getOptions(): EntityOptionsInterface;

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
