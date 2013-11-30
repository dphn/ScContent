<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Factory\Validator\Installation;

use ScContent\Validator\Installation\Migration,
    //
    Zend\ServiceManager\ServiceLocatorInterface,
    Zend\ServiceManager\FactoryInterface;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class MigrationFactory implements FactoryInterface
{
    /**
     * @param Zend\ServiceManager\ServiceLocatorInterface $validatorPluginManager
     * @return ScContent\Validator\Installation\Migration
     */
    public function createService(
        ServiceLocatorInterface $validatorPluginManager
    ) {
        $serviceLocator = $validatorPluginManager->getServiceLocator();
        $adapter = $serviceLocator->get('ScDb.Adapter');
        $validator = new Migration($adapter);
        return $validator;
    }
}
