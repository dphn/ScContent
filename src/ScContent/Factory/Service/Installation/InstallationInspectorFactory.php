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

use ScContent\Service\Installation\InstallationInspector,
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
     * @return ScContent\Service\Installation\InstallationInspector
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $validatorManager = $serviceLocator->get('ValidatorManager');
        $inspector = new InstallationInspector($validatorManager);
        return $inspector;
    }
}
