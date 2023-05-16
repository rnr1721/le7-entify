<?php

declare(strict_types=1);

namespace Core\Entify;

use Core\Entify\Interfaces\FilterLibraryInterface;
use Core\Entify\Interfaces\RulesInterface;
use Core\Entify\Interfaces\EntityHandlerDefaultInterface;
use Core\Interfaces\ValidatorInterface;
use Core\Entify\Interfaces\EntityOptionsInterface;
use function array_keys,
             array_key_exists,
             is_string,
             is_array,
             method_exists,
             array_merge,
             count;

/**
 * Default handler
 */
class EntityHandlerDefault implements EntityHandlerDefaultInterface
{

    /**
     * Library with specific filters
     * that run by DefaultHandler
     * @var FilterLibraryInterface
     */
    protected FilterLibraryInterface $filterLibrary;

    /**
     * Options object that contain bool options for DefaultHandlerInterface
     * @var EntityOptionsInterface
     */
    protected EntityOptionsInterface $options;

    /**
     * Validator that verify data using rules
     * @var ValidatorInterface
     */
    protected ValidatorInterface $validator;

    /**
     * Name of rules model
     * @var string
     */
    protected string $rulesName;

    /**
     * Some info from DataProviderInterface
     * @var array|null
     */
    protected array|null $info = null;

    /**
     * Flag - is need to refresh Info in EntityMain?
     * @var bool
     */
    protected bool $isNeedRefreshInfo = false;

    /**
     * Array with error pessages
     * Array of string data
     * @var array
     */
    protected array $errors = [];

    /**
     * Verified array with rules
     * @var array
     */
    protected array $rules;

    public function __construct(
            ValidatorInterface $validator,
            RulesInterface $rules,
            FilterLibraryInterface $filterLibrary,
            EntityOptionsInterface $options,
    )
    {
        $this->rulesName = $rules->getName();
        $this->rules = $rules->getRules();
        $this->filterLibrary = $filterLibrary;
        $this->validator = $validator;
        $this->options = $options;
    }

    /**
     * @inheritdoc
     */
    public function handle(array $all, array|null $info = null): array|null
    {

        if (is_array($info)) {
            $info['rules'] = $this->rules;
        } else {
            $info = [
                'rules' => $this->rules
            ];
        }

        $this->info = $info;

        $this->isNeedRefreshInfo = true;

        $normalizedEntity = $this->processor_normalize($all);
        if (!$normalizedEntity) {
            return null;
        }

        $standardizedEntity = $this->processor_checkNotExists($normalizedEntity);
        if ($this->options->getBoolOption('returnIfNotExistsErrors')) {
            if ($this->getErrors()) {

                return $standardizedEntity;
            }
        }

        $validate = $this->processor_validate($standardizedEntity);

        if ($this->options->getBoolOption('returnIfValidationErrors')) {
            if (!$validate) {
                return $standardizedEntity;
            }
        }

        $preFinalEntity = $this->processor_filters($standardizedEntity);

        $finalEntity = $this->processor_hide($preFinalEntity);

        return $finalEntity;
    }

    protected function processor_validate(array $all): bool
    {
        if ($this->options->getBoolOption('skipValidation')) {
            return true;
        }
        $result = true;
        foreach ($all as $entity) {
            foreach ($entity as $field => $value) {
                if (isset($this->rules[$field]['validate'])) {
                    $label = $this->rules[$field]['label'];
                    $validate = $this->rules[$field]['validate'];
                    $this->validator->setFullRule($field, $value, $validate, $label);
                }
            }
        }
        if (!$this->validator->validate()) {
            $this->addErrors($this->validator->getMessages());
            $result = false;
        }
        $this->validator->reset();
        return $result;
    }

    protected function processor_checkNotExists(array $all): array
    {
        /** @var array $entity */
        foreach ($all as &$entity) {
            foreach ($this->rules as $fieldName => $rules) {
                if (!array_key_exists($fieldName, $entity)) {
                    if (isset($rules['default'])) {
                        $entity[$fieldName] = $rules['default'];
                    } else {
                        $this->errors[] = _('Key') . ' ' . $fieldName . ' ' . _('not found in') . ' ' . $this->rulesName;
                    }
                }
            }
            if ($this->options->getBoolOption('deleteRedundant')) {
                foreach (array_keys($entity) as $key) {
                    if (!array_key_exists($key, $this->rules)) {
                        unset($entity[$key]);
                    }
                }
            }
        }
        return $all;
    }

    protected function processor_normalize(array $all): array|null
    {
        $modify = false;
        foreach ($all as $key => $value) {
            if (is_string($key)) {
                $modify = true;
            } elseif (!is_array($value)) {
                $this->errors[] = _('Incorrect array format (need key=>value)');
                return null;
            }
        }
        if ($modify) {
            return [$all];
        }
        return $all;
    }

    protected function processor_filters(array $all): array
    {
        if ($this->options->getBoolOption('skipFilters')) {
            return $all;
        }
        foreach ($all as &$entity) {
            foreach ($entity as $field => $value) {
                $filters = $this->rules[$field];
                foreach ($filters as $filter => $options) {
                    if (!$this->options->isFilterSkipped($filter)) {
                        $method = 'filter_' . $filter;
                        if (method_exists($this->filterLibrary, $method)) {
                            $entity[$field] = $this->filterLibrary->{$method}($field, $value, $options);
                        }
                    }
                }
            }
        }
        $this->addErrors($this->filterLibrary->getErrors());
        $this->filterLibrary->clearErrors();
        return $all;
    }

    /**
     * Hide some entity fields if it have hide attribute
     * @param array $all
     * @return array
     */
    protected function processor_hide(array $all): array
    {
        if ($this->options->getBoolOption('allowHideFilter')) {
            foreach ($all as &$entity) {
                /** @var string $field */
                foreach (array_keys($entity) as $field) {
                    $rules = $this->rules[$field];
                    if (isset($rules['hide']) && $rules['hide'] === true) {
                        unset($entity[$field]);
                    }
                }
            }
        }
        return $all;
    }

    protected function addErrors(array|null $errors): void
    {
        if (is_array($errors)) {
            $this->errors = array_merge($this->errors, $errors);
        }
    }

    /**
     * @inheritdoc
     */
    public function getErrors(): array|null
    {
        if (count($this->errors) === 0) {
            return null;
        }
        return $this->errors;
    }

    /**
     * @inheritdoc
     */
    public function getOptions(): EntityOptionsInterface
    {
        return $this->options;
    }

    public function getInfo(): array
    {
        return $this->info ?? [];
    }

    public function isNeedRefreshInfo(): bool
    {
        return $this->isNeedRefreshInfo;
    }

    public function clearErrors(): void
    {
        $this->errors = [];
    }

}
