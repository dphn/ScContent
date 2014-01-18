<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013-2014 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Factory\Controller\Plugin;

use ScContent\Controller\Plugin\TranslatorProxy,
    //
    Zend\ServiceManager\ServiceLocatorInterface,
    Zend\ServiceManager\FactoryInterface;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class TranslatorFactory implements FactoryInterface
{
    /**
     * @param Zend\ServiceManager\ServiceLocatorInterface $controllerPluginManager
     * @return ScContent\Controller\Plugin\TranslatorProxy
     */
    public function createService(
        ServiceLocatorInterface $controllerPluginManager
    ) {
        $serviceLocator = $controllerPluginManager
            ->getController()
            ->getServiceLocator();

        $translator = $serviceLocator->get('translator');

        $plugin = new TranslatorProxy($translator);
        return $plugin;
    }
}
