<?php

declare(strict_types=1);

namespace Core\Entify;

use Core\Interfaces\ValidatorInterface;
use Core\Entify\Interfaces\FilterLibraryInterface;
use Core\Entify\Interfaces\EntityHandlersInterface;
use Core\Entify\Interfaces\EntityHandlerDefaultInterface;
use Core\Entify\Interfaces\RulesInterface;
use Core\Entify\Interfaces\HandlerFactoryInterface;
use Core\Entify\Interfaces\EntityOptionsInterface;
use Core\Entify\DefaultFilterLibrary;
use Core\Entify\EntityOptions;
use Core\Entify\EntityHandlerDefault;
use Core\Entify\EntityHandlers;
use Core\Utils\ValidatorFactory;

/**
 * Factory for get handlers list for processing entity
 */
class HandlerFactory implements HandlerFactoryInterface
{

    /**
     * Rules object. It contain rules for validate and process Entity
     * Rules can be classes or arrays or json or xml files etc...
     * @var RulesInterface
     */
    protected RulesInterface $rules;

    public function __construct(RulesInterface $rules)
    {
        $this->rules = $rules;
    }

    public function getHandlers(): EntityHandlersInterface
    {
        return new EntityHandlers($this->getHandler());
    }

    protected function getHandler(): EntityHandlerDefaultInterface
    {
        return new EntityHandlerDefault(
                $this->getValidator(),
                $this->rules,
                $this->getFilterLibrary($this->rules->getName()),
                $this->getOptions()
        );
    }

    protected function getOptions(): EntityOptionsInterface
    {
        return new EntityOptions();
    }

    protected function getValidator(): ValidatorInterface
    {
        $factory = new ValidatorFactory();
        return $factory->getValidator();
    }

    protected function getFilterLibrary(string $rulesName): FilterLibraryInterface
    {
        return new DefaultFilterLibrary($rulesName);
    }

}
