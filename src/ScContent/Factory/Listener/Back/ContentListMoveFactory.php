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

use ScContent\Listener\Back\ContentListMove,
    //
    Zend\ServiceManager\ServiceLocatorInterface,
    Zend\ServiceManager\FactoryInterface;

class ContentListMoveFactory implements FactoryInterface
{
    /**
     * @param Zend\ServiceManager\ServiceLocatorInterface $serviceLocator
     * @return ScContent\Listener\Back\ContentListMove
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $translator = $serviceLocator->get('translator');
        $optionsProvider = $serviceLocator->get(
            'ScService.Back.ContentListOptionsProvider'
        );
        $mapper = $serviceLocator->get('ScMapper.Back.ContentListMove');

        $listener = new ContentListMove();

        $listener->setTranslator($translator);
        $listener->setOptionsProvider($optionsProvider);
        $listener->setMapper($mapper);

        $events = $listener->getEventManager();
        $events->attach(
            'process.move',
            function($event) use ($serviceLocator) {
                $layoutListener = $serviceLocator->get(
                    'ScListener.Back.Layout'
                );
                $layoutListener->contentRelocated($event);
            }
        );

        return $listener;
    }
}
