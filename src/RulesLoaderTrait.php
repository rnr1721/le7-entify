<?php

namespace Core\Entify;

use \RuntimeException;
use function is_array,
             array_key_exists,
             in_array,
             array_keys;

/**
 * You can use this trait for own model rules loaders.
 * It contain methods that verify rules
 */
trait RulesLoaderTrait
{

    /**
     * All available params for every field in rules
     * @var array
     */
    protected array $availableParams = array(
        'validate', 'label', 'check', 'unique', 'default', 'render',
        'convert', 'hide', 'escape', 'allowed', 'filter',
        'string', 'int', 'float', 'null', 'array', 'object', 'resource',
        'callable', 'meta'
    );
    
    /**
     * Required params for every field in rules
     * @var array
     */
    protected array $needParams = array(
        'validate', 'label'
    );

    /**
     * Check model data for correct info.
     * @param array $modelData Inveridied rules array
     * @return void
     * @throws RuntimeException
     */
    protected function checkModelData(array $modelData): void
    {
        foreach ($modelData as $item => $value) {
            if (!is_array($value)) {
                throw new RuntimeException("RulesLoader::getRules() value must be array: " . $item);
            }
            foreach ($this->needParams as $needParam) {
                if (!array_key_exists($needParam, $value)) {
                    throw new RuntimeException("RulesLoader::getRules() param " . $needParam . ' not present in ' . $item);
                }
            }
            foreach (array_keys($value) as $paramName) {
                if (!in_array($paramName, $this->availableParams)) {
                    throw new RuntimeException("RulesLoader::getRules() param " . $paramName . ' not native in ' . $item);
                }
            }
        }
    }
    
}
