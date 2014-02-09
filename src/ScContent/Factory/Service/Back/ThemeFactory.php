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

use ScContent\Service\Back\ThemeService,
    ScContent\Entity\Widget,
    //
    Zend\ServiceManager\ServiceLocatorInterface,
    Zend\ServiceManager\FactoryInterface;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class ThemeFactory implements FactoryInterface
{
    /**
     * @param Zend\ServiceManager\ServiceLocatorInterface $serviceLocator
     * @return ScContent\Service\Back\ThemeService
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $entity = new Widget();
        $translator = $serviceLocator->get('translator');
        $options = $serviceLocator->get('ScOptions.ModuleOptions');
        $layoutMapper = $serviceLocator->get('ScMapper.Installation.Layout');
        $settingsMapper = $serviceLocator->get('ScMapper.Back.Settings');

        $service = new ThemeService();

        $service->setTranslator($translator);
        $service->setModuleOptions($options);
        $service->setLayoutMapper($layoutMapper);
        $service->setSettingsMapper($settingsMapper);
        $service->setWidgetEntity($entity);

        return $service;
    }
}
