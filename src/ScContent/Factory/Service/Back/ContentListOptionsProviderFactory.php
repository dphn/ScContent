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

use ScContent\Service\Back\ContentListOptionsProvider,
    ScContent\Mapper\Back\ContentListOptions as OptionsMapper,
    //
    Zend\ServiceManager\ServiceLocatorInterface,
    Zend\ServiceManager\FactoryInterface;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class ContentListOptionsProviderFactory implements FactoryInterface
{
    /**
     * @param  \Zend\ServiceManager\ServiceLocatorInterface $serviceLocator
     * @return \ScContent\Service\Back\ContentListOptionsProvider
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $l10n = $serviceLocator->get('ScService.Localization');
        $request = $serviceLocator->get('Request');
        $router  = $serviceLocator->get('Router');
        $mapper = new OptionsMapper();

        $service = new ContentListOptionsProvider();

        $service->setOptionsMapper($mapper);
        $service->setLocalization($l10n);
        $service->setRequest($request);
        $service->setRouter($router);

        return $service;
    }
}
