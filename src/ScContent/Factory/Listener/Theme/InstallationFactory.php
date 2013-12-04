<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Factory\Listener\Theme;

use ScContent\Listener\Theme\InstallationListener,
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
     * @return ScContent\Listener\Theme\InstallationListener
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $service = $serviceLocator->get('ScService.Installation.Inspector');
        $listener = new InstallationListener();
        $listener->setInstallationInspector($service);
        return $listener;
    }
}
