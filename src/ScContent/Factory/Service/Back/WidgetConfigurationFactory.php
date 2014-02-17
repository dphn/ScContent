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

use ScContent\Service\Back\WidgetConfigurationService,
    //
    Zend\ServiceManager\ServiceLocatorInterface,
    Zend\ServiceManager\FactoryInterface;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class WidgetConfigurationFactory implements FactoryInterface
{
    /**
     * @param  \Zend\ServiceManager\ServiceLocatorInterface $serviceLocator
     * @return \ScContent\Service\Back\WidgetConfigurationService
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $translator = $serviceLocator->get('translator');
        $rolesMapper = $serviceLocator->get('ScMapper.Roles');
        $widgetMapper = $serviceLocator->get('ScMapper.Back.Widget');

        $service = new WidgetConfigurationService();

        $service->setTranslator($translator);
        $service->setRolesMapper($rolesMapper);
        $service->setWidgetMapper($widgetMapper);

        return $service;
    }
}
