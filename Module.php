<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013-2014 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent;

use ZfcBase\Module\AbstractModule,
    //
    Zend\ModuleManager\ModuleManagerInterface,
    Zend\EventManager\EventInterface,
    Zend\ModuleManager\Feature;

if (0 > version_compare(phpversion(), '5.4.0')) {
    exit(
        'The module ScContent need PHP version >= 5.4.0'
    );
}

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class Module extends AbstractModule implements
    Feature\InitProviderInterface,
    Feature\BootstrapListenerInterface,
    Feature\ConfigProviderInterface,
    Feature\ServiceProviderInterface,
    Feature\ControllerProviderInterface,
    Feature\ControllerPluginProviderInterface,
    Feature\FormElementProviderInterface,
    Feature\FilterProviderInterface,
    Feature\ValidatorProviderInterface,
    Feature\ViewHelperProviderInterface
{
    /**
     * @return string
     */
    public function getDir()
    {
        return __DIR__;
    }

    /**
     * @return string
     */
    public function getNamespace()
    {
        return __NAMESPACE__;
    }

    /**
     * @param  \Zend\ModuleManager\ModuleManagerInterface
     * @return void
     */
    public function init(ModuleManagerInterface $moduleManager)
    {
        define ('SC_VERSION', '0.1.3');

        defined('DS') || define('DS', DIRECTORY_SEPARATOR);

        if (! defined('DEBUG_MODE')) {
            $environment = strtolower(getenv('APPLICATION_ENV'));
            $mode = $environment === 'development' ? true : false;
            define('DEBUG_MODE', $mode);
        }

        if (DEBUG_MODE) {
            error_reporting(E_ALL | E_STRICT);
        }

        $eventManager = $moduleManager->getEventManager();
        $eventManager->attachAggregate(
            new \ScContent\Listener\ConfigListenerAggregate()
        );
    }

    /**
     * @param  \Zend\EventManager\EventInterface $event
     * @return void
     */
    public function onBootstrap(EventInterface $event)
    {
        include __DIR__ . DS . 'config' . DS . 'bootstrap.php';
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        return include __DIR__ . DS . 'config' . DS . 'module.config.php';
    }

    /**
     * @return array
     */
    public function getControllerConfig()
    {
        return include __DIR__ . DS . 'config' . DS . 'controllers.config.php';
    }

    /**
     * @return array
     */
    public function getControllerPluginConfig()
    {
        return include __DIR__ . DS . 'config' . DS . 'controller.plugins.config.php';
    }

    /**
     * @return array
     */
    public function getServiceConfig()
    {
        return include __DIR__ . DS . 'config' . DS . 'services.config.php';
    }

    /**
     * @return array
     */
    public function getFormElementConfig()
    {
        return include __DIR__ . DS . 'config' . DS . 'form.elements.config.php';
    }

    /**
     * @return array
     */
    public function getFilterConfig()
    {
        return include __DIR__ . DS . 'config' . DS . 'filters.config.php';
    }

    /**
     * @return array
     */
    public function getValidatorConfig()
    {
        return include __DIR__ . DS . 'config' . DS . 'validators.config.php';
    }

    /**
     * @return array
     */
    public function getViewHelperConfig()
    {
        return include __DIR__ . DS . 'config' . DS . 'view.helpers.config.php';
    }
}
