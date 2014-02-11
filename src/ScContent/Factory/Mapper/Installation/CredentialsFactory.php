<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013-2014 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Factory\Mapper\Installation;

use ScContent\Mapper\Installation\CredentialsMapper,
    //
    Zend\ServiceManager\ServiceLocatorInterface,
    Zend\ServiceManager\FactoryInterface;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class CredentialsFactory implements FactoryInterface
{
    /**
     * @param Zend\ServiceManager\ServiceLocatorInterface $serviceLocator
     * @return ScContent\Mapper\Installation\CredentialsMapper
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $dir = $serviceLocator->get('ScService.Dir');
        $mapper = new CredentialsMapper();
        $mapper->setDir($dir);
        return $mapper;
    }
}
