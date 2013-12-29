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

use ScContent\Service\Installation\AccountService,
    //
    Zend\ServiceManager\ServiceLocatorInterface,
    Zend\ServiceManager\FactoryInterface;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class AccountFactory implements FactoryInterface
{
    /**
     * @param Zend\ServiceManager\ServiceLocatorInterface $serviceLocator
     * @return ScContent\Service\Installation\AccountService
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $translator = $serviceLocator->get('translator');
        $zfcUserOptions = $serviceLocator->get('zfcuser_module_options');
        $zfcUserMapper = $serviceLocator->get('zfcuser_user_mapper');

        $service = new AccountService();

        $service->setTranslator($translator);
        $service->setZfcUserOptions($zfcUserOptions);
        $service->setUserMapper($zfcUserMapper);

        $listener = $serviceLocator->get('ScListener.Front.Registration');
        $listener->setRegistrationRole('admin');
        $eventManager = $service->getEventManager();
        $eventManager->attach(
            'createAccount.post',
            [$listener, 'update']
        );

        return $service;
    }
}
