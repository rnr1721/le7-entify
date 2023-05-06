<?php

declare(strict_types=1);

namespace Core\Entify\Interfaces;

/**
 * Filter library. This methods run while working DefaultHandlerInterface.
 * It read rules and launch this methods. For example, rule "check" with
 * callable value will runs filter_check() method.
 */
interface FilterLibraryInterface
{

    /**
     * Check with own function. Run callable that return true
     * if done or string with error
     * @param string $field
     * @param mixed $value
     * @param mixed $options Must be callable
     * @return mixed
     */
    public function filter_check(string $field, mixed $value, mixed $options): mixed;

    /**
     * filter value, with own callable, that get value and return modified value
     * @param string $field
     * @param mixed $value
     * @param mixed $options Must be callable or false
     * @return mixed
     */
    public function filter_filter(string $field, mixed $value, mixed $options): mixed;

    /**
     * Escape entity with htmlspecialchars
     * @param string $field
     * @param mixed $value
     * @param mixed $options Can be bool
     * @return mixed
     */
    public function filter_escape(string $field, mixed $value, mixed $options): mixed;

    /**
     * Apply strip_tags
     * @param string $field
     * @param mixed $value
     * @param mixed $options false or allowed as in strip_tags function
     * @return mixed
     */
    public function filter_allowed(string $field, mixed $value, mixed $options): mixed;

    /**
     * Convert to some type
     * @param string $field
     * @param mixed $value
     * @param mixed $options String with type - int, string, float etc
     * @return mixed
     */
    public function filter_convert(string $field, mixed $value, mixed $options): mixed;

    public function filter_bool(string $field, mixed $value, mixed $options): mixed;

    public function filter_string(string $field, mixed $value, mixed $options): mixed;

    public function filter_int(string $field, mixed $value, mixed $options): mixed;

    public function filter_float(string $field, mixed $value, mixed $options): mixed;

    public function filter_array(string $field, mixed $value, mixed $options): mixed;

    public function filter_null(string $field, mixed $value, mixed $options): mixed;

    public function filter_object(string $field, mixed $value, mixed $options): mixed;

    public function filter_callable(string $field, mixed $value, mixed $options): mixed;
    
    /**
     * Get errors array or null if no errors
     * @return array|null
     */
    public function getErrors() : array|null;
}
