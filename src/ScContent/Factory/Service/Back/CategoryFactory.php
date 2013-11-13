<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Factory\Service\Back;

use ScContent\Service\Back\CategoryService,
    //
    Zend\ServiceManager\ServiceLocatorInterface,
    Zend\ServiceManager\FactoryInterface;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class CategoryFactory implements FactoryInterface
{
    /**
     * @param Zend\ServiceManager\ServiceLocatorInterface $serviceLocator
     * @return ScContent\Service\Back\CategoryService
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $translator = $serviceLocator->get('translator');
        $authentication = $serviceLocator->get('zfcuser_auth_service');
        $contentMapper = $serviceLocator->get('sc-mapper.back.content');
        $moduleOptions = $serviceLocator->get('sc-options.module');
        $datetime = $serviceLocator->get('sc-service.datetime');

        $service = new CategoryService();

        $service->setTranslator($translator);
        $service->setAuthenticationService($authentication);
        $service->setContentMapper($contentMapper);
        $service->setModuleOptions($moduleOptions);
        $service->setDateTime($datetime);

        $events = $service->getEventManager();
        $events->attach(
            'makeCategory',
            function($event) use($serviceLocator) {
                $layout = $serviceLocator->get('sc-listener.back.layout');
                $layout->contentCreated($event);
            }
        );

        return $service;
    }
}
