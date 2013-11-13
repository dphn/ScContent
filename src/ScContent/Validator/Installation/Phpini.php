<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Validator\Installation;

use Zend\Validator\Exception\InvalidArgumentException,
    Zend\Validator\AbstractValidator;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class Phpini extends AbstractValidator
{
    /**
     * @param array $options
     * @throws Zend\Validator\Exception\InvalidArgumentException
     * @return boolean
     */
    public function isValid($options)
    {
        if (isset($options['name']) || isset($options['value'])) {
            $options = array($options);
        }
        foreach ($options as &$requirement) {
            if (! isset($requirement['name'])) {
                throw new InvalidArgumentException(
                    "Missing option 'name'."
                );
            }
            if (! isset($requirement['validation_type'])) {
                throw new InvalidArgumentException(
                    "Missing option 'validation_type'."
                );
            }
            if (! isset($requirement['validation_value'])) {
                throw new InvalidArgumentException(
                    "Missing option 'validation_value'."
                );
            }
            $value = ini_get($requirement['name']);
            switch ($requirement['validation_type']) {
                case 'expect':
                    if (! $this->validateExpect(
                            $value,
                            $requirement['validation_value']
                    )) {
                        return false;
                    }
                    break;
                case 'less_then':
                    $filteredValue = filter_var(
                        $value,
                        FILTER_SANITIZE_NUMBER_FLOAT
                    );
                    if (false === $filteredValue) {
                        throw new InvalidArgumentException(sprintf(
                            "Unexpected value '%s' parameter '%s'. The value must be a number.",
                            $value,
                            $requirement['name']
                        ));
                    }
                    if ($filteredValue > $requirement['validation_value']) {
                        return false;
                    }
                    break;
                case 'greater_then':
                    $noLimit = null;
                    if (isset($requirement['no_limit'])) {
                        $noLimit = $requirement['no_limit'];
                    }
                    $filteredValue = filter_var(
                        $value,
                        FILTER_SANITIZE_NUMBER_FLOAT
                    );
                    if (false === $filteredValue) {
                        throw new InvalidArgumentException(sprintf(
                            "Unexpected value '%s' parameter '%s'. The value must be a number.",
                            $value,
                            $requirement['name']
                        ));
                    }
                    if (! $this->validateGreaterThen(
                            $filteredValue,
                            $requirement['validation_value'],
                            $noLimit
                    )) {
                        return false;
                    }
                    break;
                default:
                    throw new InvalidArgumentException(sprintf(
                        "Unknown validation type '%s'.",
                        $requirement['validation_type']
                    ));
            }
        }
        return true;
    }

    /**
     * @param mixed $currentValue
     * @param mixed $expectedValues
     * @return boolean
     */
    protected function validateExpect($currentValue, $expectedValues)
    {
        if (! is_array($expectedValues)) {
            $expectedValues = array($expectedValues);
        }
        foreach ($expectedValues as $expectedValue) {
            if ($currentValue === $expectedValue) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param mixed $currentValue
     * @param mixed $compareValue
     * @param mixed $noLimit
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
