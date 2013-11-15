<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Factory\Mapper\Back;

use ScContent\Mapper\Back\LayoutReorderMapper,
    //
    Zend\ServiceManager\ServiceLocatorInterface,
    Zend\ServiceManager\FactoryInterface;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class LayoutReorderMapperFactory implements FactoryInterface
{
    /**
     * @param Zend\ServiceManager\ServiceLocatorInterface $serviceLocator
     * @return ScContent\Mapper\Back\LayoutReorderMapper
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $moduleOptions = $serviceLocator->get('sc-options.module');
        $adapter = $serviceLocator->get('sc-db.adapter');
        $mapper = new LayoutReorderMapper($adapter, $moduleOptions);
        return $mapper;
    }
}
