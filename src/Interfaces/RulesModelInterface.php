<?php

declare(strict_types=1);

namespace Core\Entify\Interfaces;

/**
 * This interface for users that store rules in model classes.
 * For example you can ctore models in App\Rules namespace.
 * It have method that give us rules array
 */
interface RulesModelInterface
{

    /**
     * Get array with rules
     * @return array
     */
    public function getRules(): array;
    
}
