<?php

declare(strict_types=1);

namespace Core\Entify;

use Core\Entify\Interfaces\EntityOptionsInterface;
use function property_exists;

/**
 * Default handler options for process Entity
 */
class EntityOptions implements EntityOptionsInterface
{

    private bool $returnIfNotExistsErrors = true;
    private bool $returnIfValidationErrors = false;
    private bool $skipFilters = false;
    private bool $skipValidation = false;
    private bool $deleteRedundant = true;

    /**
     * @inheritdoc
     */
    public function setDeleteRedundant(bool $deleteRedundant): self
    {
        $this->deleteRedundant = $deleteRedundant;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setSkipValidation(bool $skipValidation): self
    {
        $this->skipValidation = $skipValidation;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setReturnIfValidationErrors(bool $returnIfValidationErrors): self
    {
        $this->returnIfValidationErrors = $returnIfValidationErrors;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setReturnIfNotExistsErrors(bool $returnIfNotExistsErrors): self
    {
        $this->returnIfNotExistsErrors = $returnIfNotExistsErrors;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setSkipFilters(bool $skipFilters): self
    {
        $this->skipFilters = $skipFilters;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getBoolOption(string $optionName): bool
    {
        if (property_exists($this, $optionName)) {
            return $this->{$optionName};
        }
        return false;
    }

}
