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
        $options = $serviceLocator->get('sc-options.module');
        $mapper = $serviceLocator->get('sc-mapper.installation.layout');

        $service = new LayoutService();

        $service->setModuleOptions($options);
        $service->setLayoutMapper($mapper);
        $service->setWidgetEntity($entity);

        return $service;
    }
}
