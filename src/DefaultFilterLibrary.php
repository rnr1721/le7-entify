<?php

declare(strict_types=1);

namespace Core\Entify;

use Core\Entify\Interfaces\FilterLibraryInterface;
use \RuntimeException;
use function count,
             is_callable,
             is_string,
             is_bool,
             is_object,
             is_null,
             is_resource,
             is_float,
             in_array,
             strip_tags,
             htmlspecialchars,
             settype;

/**
 * Default filter library. This class contain filters,
 * that runs by queue (defined in default handler rules)
 */
class DefaultFilterLibrary implements FilterLibraryInterface
{

    private string $rulesName;
    private array $errors = [];

    public function __construct(string $rulesName)
    {
        $this->rulesName = $rulesName;
    }

    /**
     * @inheritdoc
     */
    public function filter_check(string $field, mixed $value, mixed $options): mixed
    {
        if ($options === false) {
            return $value;
        }

        if (is_callable($options)) {
            $result = $options($value);
            if ($result === true) {
                return $value;
            }

            if (is_string($result)) {
                $this->errors[] = $result;
            } else {
                throw new RuntimeException($this->rulesName . ', ' . $field . ' check filter value must be callable and return string error or true');
            }
        } else {
            throw new RuntimeException($this->rulesName . ', ' . $field . ' check value filter value must be callable or false');
        }
        return $value;
    }

    /**
     * @inheritdoc
     */
    public function filter_filter(string $field, mixed $value, mixed $options): mixed
    {

        if ($options === false) {
            return $value;
        }

        if (!is_callable($options)) {
            throw new RuntimeException($this->rulesName . ', ' . $field . ' filter value filter value must be callable or false');
        }

        return $options($value);
    }

    /**
     * @inheritdoc
     */
    public function filter_escape(string $field, mixed $value, mixed $options): mixed
    {
        if (!is_bool($options)) {
            throw new RuntimeException($this->rulesName . ', ' . $field . ' escape filter value must be bool');
        }
        if (!is_string($value)) {
            throw new RuntimeException($this->rulesName . ', ' . $field . ' value must be string');
        }
        if ($options === false) {
            return $value;
        }
        return htmlspecialchars($value);
    }

    /**
     * @inheritdoc
     */
    public function filter_allowed(string $field, mixed $value, mixed $options): mixed
    {
        if (!is_string($options) && !is_null($options)) {
            throw new RuntimeException($this->rulesName . ', ' . $field . ' allowed filter value must be string or null');
        }
        if (!is_string($value)) {
            throw new RuntimeException($this->rulesName . ', ' . $field . ' value must be string');
        }
        return strip_tags($value, $options);
    }

    /**
     * @inheritdoc
     */
    public function filter_convert(string $field, mixed $value, mixed $options): mixed
    {
        $allowedTypes = ['string', 'int', 'float', 'double', 'bool'];
        if (!in_array($options, $allowedTypes)) {
            throw new RuntimeException($this->rulesName . ', ' . $field . ' convert allow ' . implode(', ', $allowedTypes));
        }
        settype($value, $options);
        return $value;
    }

    /**
     * @inheritdoc
     */
    public function filter_array(string $field, mixed $value, mixed $options): mixed
    {
        if ($options !== null && is_array($value) !== $options) {
            $error = $options ? _("must be array") : _("must not be array");
            throw new RuntimeException("$this->rulesName, $field $error");
        }
        return $value;
    }

    /**
     * @inheritdoc
     */
    public function filter_bool(string $field, mixed $value, mixed $options): mixed
    {
        if ($options !== null && is_bool($value) !== $options) {
            $error = $options ? _("must be bool") : _("must not be bool");
            throw new RuntimeException("$this->rulesName, $field $error");
        }
        return $value;
    }

    /**
     * @inheritdoc
     */
    public function filter_callable(string $field, mixed $value, mixed $options): mixed
    {
        if ($options !== null && is_callable($value) !== $options) {
            $error = $options ? _("must be callable") : _("must not be callable");
            throw new RuntimeException("$this->rulesName, $field $error");
        }
        return $value;
    }

    /**
     * @inheritdoc
     */
    public function filter_float(string $field, mixed $value, mixed $options): mixed
    {
        if ($options !== null && is_float($value) !== $options) {
            $error = $options ? _("must be float") : _("must not be float");
            throw new RuntimeException("$this->rulesName, $field $error");
        }
        return $value;
    }

    /**
     * @inheritdoc
     */
    public function filter_int(string $field, mixed $value, mixed $options): mixed
    {
        if ($options !== null && is_int($value) !== $options) {
            $error = $options ? _("must be int") : _("must not be int");
            throw new RuntimeException("$this->rulesName, $field $error");
        }
        return $value;
    }

    /**
     * @inheritdoc
     */
    public function filter_null(string $field, mixed $value, mixed $options): mixed
    {
        if ($options !== null && is_null($value) !== $options) {
            $error = $options ? _("must be null") : _("must not be null");
            throw new RuntimeException("$this->rulesName, $field $error");
        }
        return $value;
    }

    /**
     * @inheritdoc
     */
    public function filter_object(string $field, mixed $value, mixed $options): mixed
    {
        if ($options !== null && is_object($value) !== $options) {
            $error = $options ? _("must be object") : _("must not be object");
            throw new RuntimeException("$this->rulesName, $field $error");
        }
        return $value;
    }

    /**
     * @inheritdoc
     */
    public function filter_resource(string $field, mixed $value, mixed $options): mixed
    {
        if ($options !== null && is_resource($value) !== $options) {
            $error = $options ? _("must be resource") : _("must not be resource");
            throw new RuntimeException("$this->rulesName, $field $error");
        }
        return $value;
    }

    /**
     * @inheritdoc
     */
    public function filter_string(string $field, mixed $value, mixed $options): mixed
    {
        if ($options !== null && is_string($value) !== $options) {
            $error = $options ? _("must be string") : _("must not be string");
            throw new RuntimeException("$this->rulesName, $field $error");
        }
        return $value;
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

}
