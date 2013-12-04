<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Factory\Options;

use ScContent\Options\ModuleOptions,
    ScContent\Module,
    //
    Zend\ServiceManager\ServiceLocatorInterface,
    Zend\ServiceManager\FactoryInterface,
    Zend\Stdlib\ArrayUtils;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class ModuleFactory implements FactoryInterface
{
    /**
     * @param Zend\ServiceManager\ServiceLocatorInterface $serviceLocator
     * @return ScContent\Options\ModuleOptions
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $baseDir = Module::getDir();
        $settings = include(
            $baseDir . DS . 'settings' . DS . 'module.settings.php'
        );

        $config = $serviceLocator->get('Config');
        $options = isset($config['sc']) ? $config['sc'] : array();

        // [1] Rewriting some module options using fixed module settings.
        $options['frontend_theme_name'] = $settings['frontend_theme_name'];
        $options['backend_theme_name'] = $settings['backend_theme_name'];

        // [2] Initialize module options.
        $moduleOptions = new ModuleOptions($options);

        return $moduleOptions;
    }
}
