<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Factory\Service\Installation;

use ScContent\Service\Installation\LayoutService,
    ScContent\Entity\Installation\WidgetEntity,
    //
    Zend\ServiceManager\ServiceLocatorInterface,
    Zend\ServiceManager\FactoryInterface;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class LayoutFactory implements FactoryInterface
{
    /**
     * @param Zend\ServiceManager\ServiceLocatorInterface $serviceLocator
     * @return ScContent\Service\Installation\LayoutService
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $entity = new WidgetEntity();
        $translator = $serviceLocator->get('translator');
        $options = $serviceLocator->get('ScOptions.ModuleOptions');
        $mapper = $serviceLocator->get('ScMapper.Installation.Layout');

        $service = new LayoutService();

        $service->setTranslator($translator);
        $service->setModuleOptions($options);
        $service->setLayoutMapper($mapper);
        $service->setWidgetEntity($entity);

        return $service;
    }
}
