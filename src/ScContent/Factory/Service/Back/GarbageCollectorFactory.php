<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013-2014 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Factory\Service\Back;

use ScContent\Service\Back\GarbageCollector,
    //
    Zend\ServiceManager\ServiceLocatorInterface,
    Zend\ServiceManager\FactoryInterface;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class GarbageCollectorFactory implements FactoryInterface
{
    /**
     * @param Zend\ServiceManager\ServiceLocatorInterface $serviceLocator
     * @return ScContent\Service\Back\GarbageCollector
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $translator = $serviceLocator->get('translator');
        $garbageMapper = $serviceLocator->get('ScMapper.Back.Garbage');
        $dir = $serviceLocator->get('ScService.Dir');

        $service = new GarbageCollector();

        $service->setTranslator($translator);
        $service->setGarbageMapper($garbageMapper);
        $service->setDir($dir);

        return $service;
    }
}
