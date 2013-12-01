<?php

namespace ScContent\Listener\Theme;

use ScContent\Options\ModuleOptions,
    ScContent\Exception\IoCException,
    //
    Zend\Filter\Word\CamelCaseToDash,
    Zend\Mvc\MvcEvent;

abstract class AbstractThemeListener
{
    /**
     * @var ScContent\Options\ModuleOptions
     */
    protected $moduleOptions;

    /**
     * @var Zend\Filter\Word\CamelCaseToDash
     */
    protected $inflector;

    /**
     * @param ScContent\Options\ModuleOptions $options
     */
    public function setModuleOptions(ModuleOptions $options)
    {
        $this->moduleOptions = $options;
    }

    /**
     * @throws ScContent\Exception\IoCException
     * @return ScContent\Options\ModuleOptions
     */
    public function getModuleOptions()
    {
        if(!$this->moduleOptions instanceof ModuleOptions) {
            throw new IoCException(
                'The module options were not set.'
            );
        }
        return $this->moduleOptions;
    }

    /**
     * @param Zend\Mvc\MvcEvent $event
     */
    public abstract function update(MvcEvent $event);

    /**
     * @param string $name
     * @return string
     */
    protected function inflectName($name)
    {
        if(!$this->inflector instanceof CamelCaseToDash) {
            $this->inflector = new CamelCaseToDash();
        }
        $name = $this->inflector->filter($name);
        return strtolower($name);
    }

    /**
     * @param string | object $controller
     * @return string
     */
    protected function deriveControllerClass($controller)
    {
        if(is_object($controller)) {
            $controller = get_class($controller);
        }

        if(strstr($controller, '\\')) {
            $controller = substr($controller, strrpos($controller, '\\') + 1);
        }

        if((10 < strlen($controller))
            && ('Controller' == substr($controller, -10))
        ) {
            $controller = substr($controller, 0, -10);
        }

        return $controller;
    }
}
