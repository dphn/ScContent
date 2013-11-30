<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent;

use Zend\ModuleManager\ModuleManager,
    Zend\Mvc\MvcEvent,
    //
    Locale;

if (0 > version_compare(phpversion(), '5.4.0')) {
    exit(
	   'The module ScContent need PHP version >= 5.4.0'
    );
}

/**
 * Short alias for DIRECTORY_SEPARATOR
 *
 * @const string
 */
defined('DS') || define('DS', DIRECTORY_SEPARATOR);

/**
 * The root directory of the module ScContent
 *
 * @const string
 */
define('SCCONTENT_BASE_DIR', __DIR__);

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class Module
{
    /**
     * @param Zend\Mvc\MvcEvent $e
     */
    public function onBootstrap(MvcEvent $e)
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

    /**
     * @return array
     */
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . DS . 'autoload_classmap.php'
            )
        );
    }
}
