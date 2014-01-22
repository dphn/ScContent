<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013-2014 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Factory\Listener\Theme;

use ScContent\Listener\Theme\FrontendStrategy,
    //
    Zend\ServiceManager\ServiceLocatorInterface,
    Zend\ServiceManager\FactoryInterface;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class FrontendStrategyFactory implements FactoryInterface
{
    /**
     * @param Zend\ServiceManager\ServiceLocatorInterface $serviceLocator
     * @return ScContent\Listener\Theme\FrontendStrategy
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $controllerManager = $serviceLocator->get('ControllerLoader');
        $viewManager = $serviceLocator->get('viewManager');
        $moduleOptions = $serviceLocator->get('ScOptions.ModuleOptions');
        $contentService = $serviceLocator->get('ScService.Front.ContentService');
        $layoutMapper = $serviceLocator->get('ScMapper.Theme.FrontendLayoutMapper');

        $strategy = new FrontendStrategy();

        $strategy->setControllerManager($controllerManager);
        $strategy->setViewManager($viewManager);
        $strategy->setModuleOptions($moduleOptions);
        $strategy->setContentService($contentService);
        $strategy->setLayoutMapper($layoutMapper);

        return $strategy;
    }
}
