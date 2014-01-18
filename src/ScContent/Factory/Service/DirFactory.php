<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013-2014 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Factory\Service;

use ScContent\Service\Dir,
    //
    Zend\ServiceManager\ServiceLocatorInterface,
    Zend\ServiceManager\FactoryInterface;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class DirFactory implements FactoryInterface
{
    /**
     * @param Zend\ServiceManager\ServiceLocatorInterface $serviceLocator
     * @return ScContent\Service\Dir
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $moduleOptions = $serviceLocator->get('ScOptions.ModuleOptions');
        $autoloadDir = $moduleOptions->getAppAutoloadDir();
        $uploadsDir = $moduleOptions->getAppUploadsDir();
        $publicDir = $moduleOptions->getAppPublicDir();

        $dir = new Dir();

        $dir->setModule(__NAMESPACE__);
        $dir->setRelativeAppAutoloadDir($autoloadDir);
        $dir->setRelativeAppPublicDir($publicDir);
        $dir->setRelativeAppUploadsDir($uploadsDir);

        return $dir;
    }
}
