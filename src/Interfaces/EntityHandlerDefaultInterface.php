<?php

declare(strict_types=1);

namespace Core\Entify\Interfaces;

/**
 * Interface for default entity handler. You can plug own handlers,
 * but by default Entify have default handler.
 */
interface EntityHandlerDefaultInterface extends EntityHandlerInterface
{

    /**
     * Get options object
     * With them, you can set up your entity
     * by calling methods
     * @return EntityOptionsInterface
     */
    public function getOptions(): EntityOptionsInterface;
}
