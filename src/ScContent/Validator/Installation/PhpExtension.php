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
    Zend\Validator\Exception\BadMethodCallException,
    Zend\Validator\AbstractValidator;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class PhpExtension extends AbstractValidator
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
            return extension_loaded($name);
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
            throw new InvalidArgumentException("Missing option 'name'.");
        }
        return $this->getValueFromCallback($options['name']);
    }
}
