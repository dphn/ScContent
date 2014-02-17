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
     * @param  \Zend\ServiceManager\ServiceLocatorInterface $serviceLocator
     * @return \ScContent\Service\Back\CategoryService
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $translator = $serviceLocator->get('translator');
        $authentication = $serviceLocator->get('zfcuser_auth_service');
        $contentMapper = $serviceLocator->get('ScMapper.Back.Content');
        $moduleOptions = $serviceLocator->get('ScOptions.ModuleOptions');
        $datetime = $serviceLocator->get('ScService.DateTime');

        $service = new CategoryService();

        $service->setTranslator($translator);
        $service->setAuthenticationService($authentication);
        $service->setContentMapper($contentMapper);
        $service->setModuleOptions($moduleOptions);
        $service->setDateTime($datetime);

        return $service;
    }
}
