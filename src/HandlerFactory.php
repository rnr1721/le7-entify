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

    /**
     * Get default handlers list for running inside EntityMain (EntityInterface)
     * @return EntityHandlersInterface
     */
    public function getHandlers(): EntityHandlersInterface
    {
        return new EntityHandlers($this->getHandler());
    }

    /**
     * Get default handler to inject in EntityHandlersInterface
     * @return EntityHandlerDefaultInterface
     */
    protected function getHandler(): EntityHandlerDefaultInterface
    {
        return new EntityHandlerDefault(
                $this->getValidator(),
                $this->rules,
                $this->getFilterLibrary($this->rules->getName()),
                $this->getOptions()
        );
    }

    /**
     * Get options method, that can set and get bool options
     * for DefaultHandler
     * @return EntityOptionsInterface
     */
    protected function getOptions(): EntityOptionsInterface
    {
        return new EntityOptions();
    }

    /**
     * Get ready-to-use ValidatorInterface
     * @return ValidatorInterface
     */
    protected function getValidator(): ValidatorInterface
    {
        $factory = new ValidatorFactory();
        return $factory->getValidator();
    }

    /**
     * Get filter library using rulesName.
     * Rules name will be used in error messages
     * @param string $rulesName
     * @return FilterLibraryInterface
     */
    protected function getFilterLibrary(string $rulesName): FilterLibraryInterface
    {
        return new DefaultFilterLibrary($rulesName);
    }

}
