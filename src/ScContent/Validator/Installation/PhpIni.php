<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013-2014 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Validator\Installation;

use Zend\Validator\Exception\InvalidArgumentException,
    Zend\Validator\AbstractValidator;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class PhpIni extends AbstractValidator
{
    /**
     * @var callable
     */
    protected $callback;

    /**
     * Constructor
     */
    public function __construct()
    {
        $callback = function($name) {
            return ini_get($name);
        };

        $this->setCallback($callback);
    }

    /**
     * @param  callable $callback
     * @throws \Zend\Validator\Exception\InvalidArgumentException
     * @return void
     */
    public function setCallback($callback)
    {
        if (! is_callable($callback)) {
            throw new InvalidArgumentException(
                'Invalid callback.'
            );
        }
        $this->callback = $callback;
    }

    /**
     * @return callable
     */
    public function getCallback()
    {
        return $this->callback;
    }

    /**
     * @param  string $name
     * @return mixed
     */
    public function getValueFromCallback($name)
    {
        $callback = $this->getCallback();
        return $callback($name);
    }

    /**
     * @param  array $options
     * @throws \Zend\Validator\Exception\InvalidArgumentException
     * @return boolean
     */
    public function isValid($options)
    {
        if (! isset($options['name'])) {
            throw new InvalidArgumentException(
                "Missing option 'name'."
            );
        }
        if (! isset($options['validation_type'])) {
            throw new InvalidArgumentException(
                "Missing option 'validation_type'."
            );
        }
        if (! isset($options['validation_value'])) {
            throw new InvalidArgumentException(
                "Missing option 'validation_value'."
            );
        }
        $value = $this->getValueFromCallback($options['name']);
        switch ($options['validation_type']) {
            case 'expect':
                if (! $this->validateExpect(
                        $value,
                        $options['validation_value']
                )) {
                    return false;
                }
                break;
            case 'less_then':
                $noLimit = null;
                if (isset($options['no_limit'])) {
                    $noLimit = $options['no_limit'];
                }
                $filteredValue = filter_var(
                    $value,
                    FILTER_SANITIZE_NUMBER_FLOAT
                );
                if (false === $filteredValue) {
                    throw new InvalidArgumentException(sprintf(
                        "Unexpected value '%s' parameter '%s'. The value must be a number.",
                        $value,
                        $options['name']
                    ));
                }
                if (! $this->validateGLessThen(
                        $filteredValue,
                        $options['validation_value'],
                        $noLimit
                )) {
                    return false;
                }
                break;
            case 'greater_then':
                $noLimit = null;
                if (isset($options['no_limit'])) {
                    $noLimit = $options['no_limit'];
                }
                $filteredValue = filter_var(
                    $value,
                    FILTER_SANITIZE_NUMBER_FLOAT
                );
                if (false === $filteredValue) {
                    throw new InvalidArgumentException(sprintf(
                        "Unexpected value '%s' parameter '%s'. The value must be a number.",
                        $value,
                        $options['name']
                    ));
                }
                if (! $this->validateGreaterThen(
                        $filteredValue,
                        $options['validation_value'],
                        $noLimit
                )) {
                    return false;
                }
                break;
            default:
                throw new InvalidArgumentException(sprintf(
                    "Unknown validation type '%s'.",
                    $options['validation_type']
                ));
        }
        return true;
    }

    /**
     * @param  mixed $currentValue
     * @param  mixed $expectedValues
     * @return boolean
     */
    protected function validateExpect($currentValue, $expectedValues)
    {
        if (! is_array($expectedValues)) {
            $expectedValues = [$expectedValues];
        }
        foreach ($expectedValues as $expectedValue) {
            if ($currentValue === $expectedValue) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param  mixed $currentValue
     * @param  mixed $compareValue
     * @param  mixed $noLimit
     * @return boolean
     */
    protected function validateLessThen(
        $currentValue,
        $compareValue,
        $noLimit = null
    ) {
        if (! is_null($noLimit) && (float) $currentValue == (float) $noLimit) {
            return false;
        }
        return $currentValue < $compareValue;
    }

    /**
     * @param  mixed $currentValue
     * @param  mixed $compareValue
     * @param  mixed $noLimit
     * @return boolean
     */
    protected function validateGreaterThen(
        $currentValue,
        $compareValue,
        $noLimit = null
    ) {
        if (! is_null($noLimit) && (float) $currentValue == (float) $noLimit) {
            return true;
        }
        return $currentValue >= $compareValue;
    }
}
