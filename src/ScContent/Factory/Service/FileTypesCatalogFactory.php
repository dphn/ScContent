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

use ScContent\Service\FileTypesCatalogInterface,
    //
    Zend\ServiceManager\ServiceLocatorInterface,
    Zend\ServiceManager\FactoryInterface,
    //
    Exception;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class FileTypesCatalogFactory implements FactoryInterface
{
    /**
     * @param  \Zend\ServiceManager\ServiceLocatorInterface $serviceLocator
     * @return \ScContent\Service\FileTransfer
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $moduleOptions = $serviceLocator->get('ScOptions.ModuleOptions');
        $catalogClass = $moduleOptions->getFileTypesCatalogClass();
        $catalog = new $catalogClass();
        if(!$catalog instanceof FileTypesCatalogInterface) {
            throw new Exception(sprintf(
                "The custom class '%s' must implement the interface 'ScContent\Service\FileTypesCatalogInterface'.",
                get_class($catalog)
            ));
        }
        return $catalog;
    }
}
