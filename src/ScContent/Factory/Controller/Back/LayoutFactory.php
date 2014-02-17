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

use ScContent\Controller\Back\LayoutController,
    ScContent\Listener\Back\LayoutAdd,
    //
    Zend\ServiceManager\ServiceLocatorInterface,
    Zend\ServiceManager\FactoryInterface;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class LayoutFactory implements FactoryInterface
{
    /**
     * @param  \Zend\ServiceManager\ServiceLocatorInterface $controllerManager
     * @return \ScContent\Controller\Back\LayoutController
     */
    public function createService(ServiceLocatorInterface $controllerManager)
    {
        $controller = new LayoutController();

        $serviceLocator = $controllerManager->getServiceLocator();
        $events = $controller->getEventManager();
        $events->attach(
            'reorder',
            function($event) use ($serviceLocator) {
                $listener = $serviceLocator->get(
                    'ScListener.Back.LayoutReorder'
                );
                return $listener->process($event);
            }
        );
        $events->attach(
            'move',
            function($event) use ($serviceLocator) {
                $listener = $serviceLocator->get(
                    'ScListener.Back.LayoutMove'
                );
                return $listener->process($event);
            }
        );
        $events->attach(
            'add',
            function($event) use ($serviceLocator) {
                $listener = new LayoutAdd();
                return $listener->process($event);
            }
        );
        return $controller;
    }
}
