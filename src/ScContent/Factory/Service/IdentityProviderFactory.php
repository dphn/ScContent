<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Factory\Service;

use ScContent\Service\IdentityProvider,
    //
    Zend\ServiceManager\ServiceLocatorInterface,
    Zend\ServiceManager\FactoryInterface;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class IdentityProviderFactory implements FactoryInterface
{
    /**
     * @param Zend\ServiceManager\ServiceLocatorInterface $serviceLocator
     * @return ScContent\Service\IdentityProvider
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $mapper = $serviceLocator->get('ScMapper.Roles');
        $userService = $serviceLocator->get('zfcuser_user_service');
        $config      = $serviceLocator->get('BjyAuthorize\Config');

        $service = new IdentityProvider();

        $service->setDefaultRole($config['default_role']);
        $service->setUserService($userService);
        $service->setMapper($mapper);
        return $service;
    }
}
