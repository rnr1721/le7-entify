<?php

declare(strict_types=1);

namespace Core\Entify\Interfaces;

/**
 * Object for store rules for processing entities
 */
interface RulesInterface
{

    /**
     * Get model name of rules
     * @return string Name of entity rules model
     */
    public function getName(): string;

    /**
     * Get rules array
     * @return array Array with rules
     */
    public function getRules(): array;
}
