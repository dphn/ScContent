<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013-2014 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Factory\Validator\Installation;

use ScContent\Validator\Installation\Roles,
    //
    Zend\ServiceManager\ServiceLocatorInterface,
    Zend\ServiceManager\FactoryInterface;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class RolesFactory implements FactoryInterface
{
    /**
     * @param Zend\ServiceManager\ServiceLocatorInterface $validatorPluginManager
     * @return ScContent\Validator\Installation\Roles
     */
    public function createService(
        ServiceLocatorInterface $validatorPluginManager
    ) {
        $serviceLocator = $validatorPluginManager->getServiceLocator();
        $mapper = $serviceLocator->get('ScMapper.Roles');

        $validator = new Roles();
        $validator->setMapper($mapper);


        return $validator;
    }
}
