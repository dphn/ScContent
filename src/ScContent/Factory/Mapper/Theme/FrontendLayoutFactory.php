<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013-2014 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Factory\Mapper\Theme;

use ScContent\Mapper\Theme\FrontendLayoutMapper,
    ScContent\Entity\Front\Regions,
    //
    Zend\ServiceManager\ServiceLocatorInterface,
    Zend\ServiceManager\FactoryInterface;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class FrontendLayoutFactory implements FactoryInterface
{
    /**
     * @param Zend\ServiceManager\ServiceLocatorInterface $serviceLocator
     * @return ScContent\Mapper\Theme\FrontendLayoutMapper
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $adapter = $serviceLocator->get('ScDb.Adapter');
        $moduleOptions = $serviceLocator->get('ScOptions.ModuleOptions');
        $identityProvider = $serviceLocator->get(
            'BjyAuthorize\Provider\Identity\ProviderInterface'
        );
        $regions = new Regions($identityProvider, $moduleOptions);

        $mapper = new FrontendLayoutMapper($adapter, $moduleOptions, $regions);

        return $mapper;
    }
}
