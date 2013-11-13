<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Factory\Service;

use ScContent\Service\ThumbnailGenerator,
    ScContent\Service\FileTransfer,
    //
    Zend\ServiceManager\ServiceLocatorInterface,
    Zend\ServiceManager\FactoryInterface;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class FileTransferFactory implements FactoryInterface
{
    /**
     * @param Zend\ServiceManager\ServiceLocatorInterface $serviceLocator
     * @return ScContent\Service\FileTransfer
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $thumbnailGenerator = new ThumbnailGenerator();
        $catalog = $serviceLocator->get('sc-service.file.types.catalog');
        $dir = $serviceLocator->get('sc-service.dir');
        $fileTransfer = new FileTransfer($thumbnailGenerator, $catalog, $dir);
        return $fileTransfer;
    }
}
