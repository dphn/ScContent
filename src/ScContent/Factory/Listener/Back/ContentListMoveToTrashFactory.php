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

use ScContent\Listener\Back\ContentListMoveToTrash,
    //
    Zend\ServiceManager\ServiceLocatorInterface,
    Zend\ServiceManager\FactoryInterface;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class ContentListMoveToTrashFactory implements FactoryInterface
{
    /**
     * @param  \Zend\ServiceManager\ServiceLocatorInterface $serviceLocator
     * @return \ScContent\Listener\Back\ContentListMoveToTrash
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $translator = $serviceLocator->get('translator');
        $optionsProvider = $serviceLocator->get(
            'ScService.Back.ContentListOptionsProvider'
        );
        $mapper = $serviceLocator->get(
            'ScMapper.Back.ContentListToggleTrash'
        );

        $listener = new ContentListMoveToTrash();

        $listener->setTranslator($translator);
        $listener->setOptionsProvider($optionsProvider);
        $listener->setMapper($mapper);

        return $listener;
    }
}
