<?php

declare(strict_types=1);

namespace Core\Entify\Interfaces;

/**
 * Options object for default handler.
 * It can store bool options as properties
 * and have methods for set options and one method for get any option
 * by property name
 */
interface EntityOptionsInterface
{

    /**
     * Delete redundant fields from entity
     * @param bool $deleteRedundant
     * Default true
     * @return self
     */
    public function setDeleteRedundant(bool $deleteRedundant): self;

    /**
     * Skip processing validator
     * @param bool $skipValidation Default false
     * @return self
     */
    public function setSkipValidation(bool $skipValidation): self;

    /**
     * Skip processing filters
     * @param bool $skipFilters
     * @return self
     */
    public function setSkipFilters(bool $skipFilters): self;

    /**
     * Return result if some fields from rules not exists in entities
     * of some errors before. In this case will be skipped validation
     * and filters if true;
     * @param bool $returnIfNotExistsErrors
     * @return self
     */
    public function setReturnIfNotExistsErrors(bool $returnIfNotExistsErrors): self;

    /**
     * If true, the result will be return
     * if validator say any errors
     * @param bool $returnIfValidationErrors
     * @return self
     */
    public function setReturnIfValidationErrors(bool $returnIfValidationErrors): self;

    /**
     * Get some bool option
     * @param string $optionName
     * @return bool
     */
    public function getBoolOption(string $optionName): bool;
}
