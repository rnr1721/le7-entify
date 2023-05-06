<?php

declare(strict_types=1);

namespace Core\Entify\Interfaces;

/**
 * Loader for rules. It load rules from source and give us RulesInterface
 */
interface RulesLoaderInterface
{

    /**
     * Get rules object
     * @param string $modelName Name of rules model
     * @param array|null $modelData Non-required data for qiock set
     * @return RulesInterface
     */
    public function getRules(string $modelName, ?array $modelData = null): RulesInterface;

}
