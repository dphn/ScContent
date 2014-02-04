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

use ScContent\Service\Back\WidgetVisibilityService,
    ScContent\Mapper\Back\WidgetVisibilityOptionsMapper,
    //
    Zend\ServiceManager\ServiceLocatorInterface,
    Zend\ServiceManager\FactoryInterface;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class WidgetVisibilityFactory implements FactoryInterface
{
    /**
     * @param Zend\ServiceManager\ServiceLocatorInterface $serviceLocator
     * @return ScContent\Service\Back\WidgetVisibilityService
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $listMapper = $serviceLocator->get('ScMapper.Back.WidgetVisibilityList');
        $searchMapper = $serviceLocator->get('ScMapper.Back.WidgetVisibilitySearch');
        $optionsMapper = new WidgetVisibilityOptionsMapper();
        $request = $serviceLocator->get('Request');
        $router  = $serviceLocator->get('Router');

        $service = new WidgetVisibilityService();

        $service->setListMapper($listMapper);
        $service->setSearchMapper($searchMapper);
        $service->setOptionsMapper($optionsMapper);
        $service->setRequest($request);
        $service->setRouter($router);

        return $service;
    }
}
