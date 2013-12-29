<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Factory\Listener\Installation;

use ScContent\Listener\Installation\InstallationInspector,
    //
    Zend\ServiceManager\ServiceLocatorInterface,
    Zend\ServiceManager\FactoryInterface;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class InstallationInspectorFactory implements FactoryInterface
{
    /**
     * @param Zend\ServiceManager\ServiceLocatorInterface $serviceLocator
     * @return ScContent\Listener\Installation\InstallationInspector
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $guardExceptionStrategy = $serviceLocator->get(
            'ScListener.GuardExceptionStrategy'
        );
        $validatorManager = $serviceLocator->get('ValidatorManager');

        $listener = new InstallationInspector();

        $listener->setValidatorManager($validatorManager);
        $listener->setGuardExceptionStrategy($guardExceptionStrategy);

        return $listener;
    }
}
