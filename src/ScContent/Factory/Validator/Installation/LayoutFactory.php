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

use ScContent\Validator\Installation\Layout,
    //
    Zend\ServiceManager\ServiceLocatorInterface,
    Zend\ServiceManager\FactoryInterface;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class LayoutFactory implements FactoryInterface
{
    /**
     * @param Zend\ServiceManager\ServiceLocatorInterface $validatorPluginManager
     * @return ScContent\Validator\Installation\Layout
     */
    public function createService(
        ServiceLocatorInterface $validatorPluginManager
    ) {
        $serviceLocator = $validatorPluginManager->getServiceLocator();
        $options = $serviceLocator->get('ScOptions.ModuleOptions');
        $mapper = $serviceLocator->get('ScMapper.Installation.Layout');

        $validator = new Layout();

        $validator->setModuleOptions($options);
        $validator->setLayoutMapper($mapper);

        return $validator;
    }
}