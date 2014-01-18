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
    //
    Zend\ServiceManager\ServiceLocatorInterface,
    Zend\ServiceManager\FactoryInterface;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class ContentListOptionsProviderFactory implements FactoryInterface
{
    /**
     * @param Zend\ServiceManager\ServiceLocatorInterface $serviceLocator
     * @return ScContent\Service\Back\ContentListOptionsProvider
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $router  = $serviceLocator->get('Router');
        $request = $serviceLocator->get('Request');
        $l10n = $serviceLocator->get('ScService.Localization');

        $service = new ContentListOptionsProvider(
            $router, $request, $l10n
        );
        return $service;
    }
}
