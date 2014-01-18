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
class PhpExtension extends AbstractValidator
{
    /**
     * @param array $options
     * @throws Zend\Validator\Exception\InvalidArgumentException
     * @return boolean
     */
    public function isValid($options)
    {
        if (isset($options['name'])) {
            $options = [$options];
        }
        foreach ($options as $requirement) {
            if (! isset($requirement['name'])) {
                throw new InvalidArgumentException("Missing option 'name'");
            }
            if (! extension_loaded($requirement['name'])) {
                return false;
            }
        }
        return true;
    }
}
