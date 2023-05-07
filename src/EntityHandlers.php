<?php

declare(strict_types=1);

namespace Core\Entify;

use Core\Entify\Interfaces\EntityOptionsInterface;
use Core\Entify\Interfaces\EntityHandlersInterface;
use Core\Entify\Interfaces\EntityHandlerInterface;
use Core\Entify\Interfaces\EntityHandlerDefaultInterface;
use function get_class,
             array_key_exists,
             in_array;

/**
 * Entity handlers manager (Get list, add and quese handlers, bypass list etc)
 */
class EntityHandlers implements EntityHandlersInterface
{

    /**
     * Default handler for handling entities
     * @var EntityHandlerDefaultInterface
     */
    protected EntityHandlerDefaultInterface $defaultHandler;
    
    /**
     * Handler list for processing entities. By default it contain
     * one default handler
     * @var array
     */
    protected array $handlers = [];
    
    /**
     * List of handlers that need to be bepassed
     * @var array
     */
    protected array $bypassHandlers = [];

    /**
     * Constructor have one parameter - default handler.
     * This handler will be added to the handlers list
     * @param EntityHandlerDefaultInterface $handler Default handler
     */
    public function __construct(EntityHandlerDefaultInterface $handler)
    {
        $this->defaultHandler = $handler;
        $this->handlers[get_class($handler)] = $handler;
    }

    /**
     * Register oen Entity handler as callable
     * @param string $key Key of handler
     * @param string $after After that?
     * @param callable $value Callable with array input
     * @return $this
     */
    public function registerHandler(string $key, string $after, callable $value): self
    {
        $newhaHandlers = [];
        foreach ($this->handlers as $handlerKey => $handlerValue) {
            $newhaHandlers[$handlerKey] = $handlerValue;
            if ($handlerKey === $after) {
                $newhaHandlers[$key] = $value;
            }
        }
        $this->handlers = $newhaHandlers;
        return $this;
    }

    /**
     * Register own Entity handler
     * @param EntityHandlerInterface $handler Handler
     * @param string $after After that of current?
     * @return self
     */
    public function registerClassHandler(
            EntityHandlerInterface $handler,
            string $after
    ): self
    {
        $newhaHandlers = [];
        foreach ($this->handlers as $handlerKey => $handlerValue) {
            $newhaHandlers[$handlerKey] = $handlerValue;
            if ($handlerKey === $after) {
                $newhaHandlers[get_class($handler)] = $handler;
            }
        }
        $this->handlers = $newhaHandlers;
        return $this;
    }

    /**
     * Mark handler as bypassed
     * @param string $key Handler key
     * @return self
     */
    public function bypassHandler(string $key): self
    {
        if (array_key_exists($key, $this->handlers)) {
            $this->bypassHandlers[] = $key;
        }
        return $this;
    }

    /**
     * Get Field handlers for process
     * @return array
     */
    public function getHandlers(): array
    {
        $result = [];
        foreach ($this->handlers as $handlerKey => $handlerValue) {
            if (!in_array($handlerKey, $this->bypassHandlers)) {
                $result[$handlerKey] = $handlerValue;
            }
        }
        return $result;
    }

    /**
     * Return options from default handler
     * @return EntityOptionsInterface
     */
    public function getOptions(): EntityOptionsInterface
    {
        return $this->defaultHandler->getOptions();
    }

    /**
     * Clear all handlers include default from handlers list to process entity
     * @return self
     */
    public function clearHandlerList(): self
    {
        $this->handlers = [];
        return $this;
    }

}
