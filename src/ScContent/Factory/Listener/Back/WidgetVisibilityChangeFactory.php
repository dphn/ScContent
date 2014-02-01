<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013-2014 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Factory\Listener\Back;

use ScContent\Listener\Back\WidgetVisibilityChange,
    //
    Zend\ServiceManager\ServiceLocatorInterface,
    Zend\ServiceManager\FactoryInterface;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class WidgetVisibilityChangeFactory implements FactoryInterface
{
    /**
     * @param Zend\ServiceManager\ServiceLocatorInterface $serviceLocator
     * @return ScContent\Listener\Back\WidgetVisibilityChange
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $translator = $serviceLocator->get('translator');
        $mapper = $serviceLocator->get('ScMapper.Back.WidgetVisibilityChange');

        $listener = new WidgetVisibilityChange();

        $listener->setTranslator($translator);
        $listener->setMapper($mapper);

        return $listener;
    }
}
