<?php

declare(strict_types=1);

namespace Core\Entify;

use Core\Entify\Rules;
use Core\Entify\Interfaces\RulesInterface;
use Core\Entify\Interfaces\ModelInterface;
use Core\Entify\Interfaces\RulesLoaderInterface;
use \RuntimeException;
use function ucfirst,
             str_replace,
             class_exists;

/**
 * Loader for rules Model using classes.
 */
class RulesLoaderClass implements RulesLoaderInterface
{

    use RulesLoaderTrait;

    /**
     * Namespace for finding rules models
     * @var string
     */
    protected string $namespace;

    /**
     * In this constructor you can set namespace for finding rules model classes
     * @param string $modelNamespace
     */
    public function __construct(string $modelNamespace = '\\')
    {
        $this->namespace = $modelNamespace;
    }

    /**
     * Find the class (ModelInterface) with rules data
     * @param string $modelName Name of rules model
     * @param string $modelNamespace Namespace for find classes
     * @return ModelInterface
     * @throws RuntimeException
     */
    private function findModel(string $modelName, string $modelNamespace): ModelInterface
    {
        $className = $modelNamespace . ucfirst(str_replace('_', '', $modelName));
        if (class_exists($className)) {
            $object = new $className();
            if (!$object instanceof ModelInterface) {
                throw new RuntimeException("RulesClass::findModel(): class " . $className . ' must be instance of ' . ModelInterface::class);
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
