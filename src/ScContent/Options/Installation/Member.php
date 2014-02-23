<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013-2014 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Options\Installation;

use ScContent\Entity\AbstractList,
    ScContent\Exception\InvalidArgumentException,
    ScContent\Exception\DomainException;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class Member extends AbstractList
{
    /**
     * @const string
     */
    const DefaultController = 'ScController.Installation.Default';

    /**
     * @const string
     */
    const DefaultAction = 'index';

    /**
     * @var string
     */
    protected $name = '';

    /**
     * @var string
     */
    protected $validator = '';

    /**
     * @var string
     */
    protected $service = '';

    /**
     * @var string
     */
    protected $controller = self::DefaultController;

    /**
     * @var string
     */
    protected $action = self::DefaultAction;

    /**
     * Batch
     *
     * @var array
     */
    protected $items = [null];

    /**
     * @param string $name
     * @param array $options
     */
    public function __construct($name, $options)
    {
        $this->name = $name;

        if (! isset($options['validator'])) {
            throw new DomainException(sprintf(
                "Missing 'validator' option for member of chain '%s'.",
                $name
            ));
        }
        $this->validator = $options['validator'];

        if (! isset($options['service'])  && ! isset($options['controller'])) {
            throw new DomainException(
                "For member of chain '%s' must be specified 'service' or 'controller'.",
                $name
            );
        }
        if (isset($options['controller']) && isset($options['action'])) {
            $this->controller = $options['controller'];
            $this->action = $options['action'];
        } else {
            $this->service = $options['service'];
        }
        if (isset($options['batch'])
            && is_array($options['batch'])
            && ! empty($options['batch'])
        ) {
            $this->items = $options['batch'];
        }
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param  string $name
     * @return void
     */
    public function setValidator($name)
    {
        $this->validator = $name;
    }

    /**
     * @return string
     */
    public function getValidator()
    {
        return $this->validator;
    }

    /**
     * @param  string $name
     * @return void
     */
    public function setService($name)
    {
        $this->service = $name;
    }

    /**
     * @return string
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * @param  string $name
     * @return void
     */
    public function setController($name)
    {
        $this->controller = $controller;
    }

    /**
     * @return string
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * @param  string $name
     * @return void
     */
    public function setAction($name)
    {
        $this->action = $name;
    }

    /**
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @param  array $batch
     * @throws \ScContent\Exception\InvalidArgumentException
     * @return void
     */
    public function setBatch($batch)
    {
        if (! is_array($batch)) {
            throw new InvalidArgumentException(
                'The batch of parameters must be an array.'
            );
        }
        $this->items = $batch;
    }

    /**
     * @return array
     */
    public function getBatch()
    {
        return $this->items;
    }
}
