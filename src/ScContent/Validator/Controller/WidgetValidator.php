<?php

namespace ScContent\Validator\Controller;


use ScContent\Exception\IoCException,
    //
    Zend\Mvc\Controller\ControllerManager,
    Zend\Validator\Exception\InvalidArgumentException,
    Zend\Validator\AbstractValidator;

class WidgetValidator extends AbstractValidator
{
    /**#@+
     * @const string
     */
    const Controller = 'controller';
    const Action     = 'action';
    const RuntimeControllerError = 'Runtime Controller Error';
    const RuntimeActionError = 'Runtime Action Error';
    /**#@-*/

    /**
     * @var Zend\Mvc\Controller\ControllerManager
     */
    protected $loader;

    /**
     * @var array
     */
    protected $messageTemplates = array(
        self::RuntimeControllerError
            => "Don’t can to get the widget, the controller '%value%' does not exist.",
        self::RuntimeActionError
            => "Don’t can to get the widget, the action '%value%' does not exist.",
    );

    /**
     * @param array $options
     */
    public function __construct($options = array())
    {
        if (! is_array($options)) {
            $options = func_get_args();
            $temp = array();
            if (! empty($options)) {
                $temp['loader'] = array_shift($options);
            }
            $options = $temp;
        }
        parent::__construct($options);
    }

    /**
     * @param Zend\Mvc\Controller\ControllerManager $loader
     */
    public function setLoader(ControllerManager $loader)
    {
        $this->loader = $loader;
    }

    /**
     * @throws ScContent\Exception\IoCException
     * @return Zend\Mvc\Controller\ControllerManager
     */
    public function getLoader()
    {
        if (! $this->loader instanceof ControllerManager) {
            throw new IoCException(
                'The Controller Manager was not set.'
            );
        }
        return $this->loader;
    }

    /**
     * @param array $options
     * @throws Zend\Validator\Exception\InvalidArgumentException
     * @return boolean
     */
    public function isValid($options)
    {
        if (! isset($options['controller'])) {
            throw new InvalidArgumentException(
                "Missing 'controller' option."
            );
        }
        if (! isset($options['action'])) {
            throw new InvalidArgumentException(
                "Missing 'action' option."
            );
        }
        $controller = $options['controller'];
        $action     = $options['action'];
        if (! $this->loader->has($controller)) {
            $this->setValue($controller);
            $this->error(self::RuntimeControllerError);
            return false;
        }
        $testObject = $this->loader->get($controller);
        $method = $testObject::getMethodFromAction($action);
        if (! method_exists($testObject, $method)) {
            $this->setValue($action);
            $this->error(self::RuntimeActionError);
            return false;
        }
        return true;
    }
}
