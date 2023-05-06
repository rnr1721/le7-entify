<?php

declare(strict_types=1);

namespace Core\Entify;

use Core\Entify\Interfaces\RulesInterface;

class Rules implements RulesInterface
{

    /**
     * Rules model name
     * @var string
     */
    private string $rulesName;

    /**
     * Array with verified rules
     * @var array
     */
    private array $rulesData;

    /**
     * This constructor arguments - rules model name and array of rules
     * @param string $rulesName
     * @param array $rulesData
     */
    public function __construct(string $rulesName, array $rulesData)
    {
        $this->rulesName = $rulesName;
        $this->rulesData = $rulesData;
    }

    /**
     * @inheritdoc
     */
    public function getName(): string
    {
        return $this->rulesName;
    }

    /**
     * @inheritdoc
     */
    public function getRules(): array
    {
        return $this->rulesData;
    }

}
