<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013-2014 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Factory\Service\Installation;

use Zend\Authentication\Storage\Session as SessionStorage,
    Zend\Authentication\AuthenticationService,
    Zend\ServiceManager\ServiceLocatorInterface,
    Zend\ServiceManager\FactoryInterface;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class AuthServiceFactory implements FactoryInterface
{
    /**
     * @param  \Zend\ServiceManager\ServiceLocatorInterface $serviceLocator
     * @return \Zend\Authentication\AuthenticationService
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $storage = new SessionStorage('ScContent_Installation_Auth_Storage');
        $adapter = $serviceLocator->get('ScService.Installation.AuthAdapter');

        $service = new AuthenticationService($storage, $adapter);

        return $service;
    }
}
