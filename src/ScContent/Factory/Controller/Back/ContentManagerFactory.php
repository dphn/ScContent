<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Factory\Controller\Back;

use ScContent\Controller\Back\ContentManagerController,
    //
    Zend\ServiceManager\ServiceLocatorInterface,
    Zend\ServiceManager\FactoryInterface;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class ContentManagerFactory implements FactoryInterface
{
    /**
     * @param Zend\ServiceManager\ServiceLocatorInterface $controllerManager
     * @return ScContent\Controller\Back\ContentManager
     */
    public function createService(ServiceLocatorInterface $controllerManager)
    {
        $controller = new ContentManagerController();

        $serviceLocator = $controllerManager->getServiceLocator();
        $listeners = $serviceLocator->get(
            'ScListener.Back.ContentListAggregate'
        );
        $events = $controller->getEventManager();
        $events->attachAggregate($listeners);

        return $controller;
    }
}
