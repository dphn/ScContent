<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013-2014 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Factory\Controller\Back;

use ScContent\Controller\Back\WidgetVisibilityController,
    //
    Zend\ServiceManager\ServiceLocatorInterface,
    Zend\ServiceManager\FactoryInterface;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class WidgetVisibilityFactory implements FactoryInterface
{
    /**
     * @param  \Zend\ServiceManager\ServiceLocatorInterface $controllerManager
     * @return \ScContent\Controller\Back\WidgetVisibilityController
     */
    public function createService(ServiceLocatorInterface $controllerManager)
    {
        $controller = new WidgetVisibilityController();

        $serviceLocator = $controllerManager->getServiceLocator();
        $events = $controller->getEventManager();

        $events->attach(
            'save',
            function($event) use ($serviceLocator) {
                $listener = $serviceLocator->get(
                    'ScListener.Back.WidgetVisibilityChange'
                );
                return $listener->process($event);
            }
        );

        return $controller;
    }
}
