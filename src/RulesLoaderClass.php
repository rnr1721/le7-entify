<?php

declare(strict_types=1);

namespace Core\Entify;

use Core\Entify\Rules;
use Core\Entify\Interfaces\RulesInterface;
use Core\Entify\Interfaces\RulesModelInterface;
use Core\Entify\Interfaces\RulesLoaderInterface;
use \RuntimeException;
use function ucfirst,
             str_replace,
             class_exists;

class RulesLoaderClass implements RulesLoaderInterface
{

    use RulesLoaderTrait;

    /**
     * Namespace for finding rules models
     * @var string
     */
    protected string $namespace;

    public function __construct(string $modelNamespace = '\\')
    {
        $this->namespace = $modelNamespace;
    }

    /**
     * Find the class (RulesModelInterface) with rules data
     * @param string $modelName Name of rules model
     * @param string $modelNamespace Namespace for find classes
     * @return RulesModelInterface
     * @throws RuntimeException
     */
    private function findModel(string $modelName, string $modelNamespace): RulesModelInterface
    {
        $className = $modelNamespace . ucfirst(str_replace('_', '', $modelName));
        if (class_exists($className)) {
            $object = new $className();
            if (!$object instanceof RulesModelInterface) {
                throw new RuntimeException("RulesClass::findModel(): class " . $className . ' must be instance of ' . RulesModelInterface::class);
            }
            return $object;
        } else {
            throw new RuntimeException("RulesClass::findModel() rules model not exists: " . $className);
        }
    }

    /**
     * @inheritdoc
     */
    public function getRules(string $modelName, ?array $modelData = null): RulesInterface
    {
        if ($modelData === null) {
            $modelClass = $this->findModel($modelName, $this->namespace);
            $modelData = $modelClass->getRules();
        }
        $this->checkModelData($modelData);
        return new Rules($modelName, $modelData);
    }

}
