<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013-2014 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Factory\Options;

use ScContent\Options\InstallationOptions,
    ScContent\Module,
    //
    Zend\ServiceManager\ServiceLocatorInterface,
    Zend\ServiceManager\FactoryInterface;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class InstallationFactory implements FactoryInterface
{
    /**
     * @param Zend\ServiceManager\ServiceLocatorInterface $serviceLocator
     * @return ScContent\Options\InstallationOptions
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $module = new Module();
        $baseDir = $module->getDir();
        $options = include(
            $baseDir . DS . 'config' . DS . 'installation.config.php'
        );
        $installationOptions = new InstallationOptions($options);
        return $installationOptions;
    }
}
