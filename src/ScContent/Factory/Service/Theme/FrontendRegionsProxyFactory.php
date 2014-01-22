<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013-2014 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Factory\Service\Theme;

use ScContent\Service\Theme\FrontendRegionsProxy,
    ScContent\Entity\Front\Regions,
    //
    Zend\ServiceManager\ServiceLocatorInterface,
    Zend\ServiceManager\FactoryInterface;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class FrontendRegionsProxyFactory implements FactoryInterface
{
    /**
     * @param Zend\ServiceManager\ServiceLocatorInterface $serviceLocator
     * @return ScContent\Service\Theme\FrontendRegionsProxy
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $moduleOptions = $serviceLocator->get('ScOptions.ModuleOptions');

        $regions = new Regions($moduleOptions);

        $identityProvider = $serviceLocator->get(
            'BjyAuthorize\Provider\Identity\ProviderInterface'
        );

        $service = new FrontendRegionsProxy();

        $service->setModuleOptions($moduleOptions);
        $service->setIdentityProvider($identityProvider);
        $service->setRegions($regions);

        return $service;
    }
}
