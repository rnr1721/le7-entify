<?php

declare(strict_types=1);

namespace Core\Entify\Interfaces;

/**
 * Interface hor Handlers list object. This object can edit and get
 * list of handlers that process entity
 */
interface EntityHandlersInterface
{

    /**
     * Register new handler
     * @param string $key
     * @param string $after Key afrer that run this handler
     * @param callable $value
     * @return self
     */
    public function registerHandler(string $key, string $after, callable $value): self;

    /**
     * 
     * @param EntityHandlerInterface $handler
     * @param string $after Key after that run this handler
     * @return self
     */
    public function registerClassHandler(
            EntityHandlerInterface $handler,
            string $after
    ): self;

    /**
     * Get default handler options object
     * @return EntityOptionsInterface
     */
    public function getOptions(): EntityOptionsInterface;

    /**
     * Clear all handlers list, with default
     * @return self
     */
    public function clearHandlerList(): self;

    /**
     * Bypass some handler by key
     * @param string $key Handler key (classname)
     * @return self
     */
    public function bypassHandler(string $key): self;

    /**
     * Get handler list
     * This method return array of EnrityHandlerInterface
     * @return array
     */
    public function getHandlers(): array;
}
