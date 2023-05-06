<?php

declare(strict_types=1);

namespace Core\Entify\Interfaces;

/**
 * The factory that give us the handlers list for processing entity
 */
interface HandlerFactoryInterface
{
    
    /**
     * Get set of handlers for process entity rules
     * @return EntityHandlersInterface
     */
    public function getHandlers(): EntityHandlersInterface;
    
}
