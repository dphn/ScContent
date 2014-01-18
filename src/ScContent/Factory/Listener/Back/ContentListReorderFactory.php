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

use ScContent\Listener\Back\ContentListReorder,
    //
    Zend\ServiceManager\ServiceLocatorInterface,
    Zend\ServiceManager\FactoryInterface;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class ContentListReorderFactory implements FactoryInterface
{
    /**
     * @param Zend\ServiceManager\ServiceLocatorInterface $serviceLocator
     * @return ScContent\Listener\Back\ContentListReorder
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $translator = $serviceLocator->get('translator');
        $optionsProvider = $serviceLocator->get(
            'ScService.Back.ContentListOptionsProvider'
        );
        $mapper = $serviceLocator->get('ScMapper.Back.ContentListReorder');

        $listener = new ContentListReorder();

        $listener->setTranslator($translator);
        $listener->setOptionsProvider($optionsProvider);
        $listener->setMapper($mapper);

        return $listener;
    }
}
