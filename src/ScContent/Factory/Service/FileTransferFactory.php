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

use ScContent\Service\ThumbnailGenerator,
    ScContent\Service\FileTransfer,
    //
    Zend\Validator\Db\NoRecordExists,
    Zend\ServiceManager\ServiceLocatorInterface,
    Zend\ServiceManager\FactoryInterface;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class FileTransferFactory implements FactoryInterface
{
    /**
     * @param  \Zend\ServiceManager\ServiceLocatorInterface $serviceLocator
     * @return \ScContent\Service\FileTransfer
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $translator = $serviceLocator->get('translator');
        $catalog = $serviceLocator->get('ScService.FileTypesCatalog');
        $dir = $serviceLocator->get('ScService.Dir');

        $adapter = $serviceLocator->get('ScDb.Adapter');
        $validator = new NoRecordExists([
            'adapter' => $adapter,
            'table'   => 'sc_content',
            'field'   => 'name',
        ]);

        $service = new FileTransfer();

        $service->setTranslator($translator);
        $service->setValidator($validator);
        $service->setCatalog($catalog);
        $service->setDir($dir);

        $events = $service->getEventManager();
        $events->attach(
            'receive',
            function($event) use ($serviceLocator) {
                $listener = $serviceLocator->get(
                    'ScListener.ThumbnailListener'
                );
                return $listener->generate($event);
            }
        );
        $events->attach(
            'rollBack',
            function($event) use ($serviceLocator) {
                $listener = $serviceLocator->get(
                    'ScListener.ThumbnailListener'
                );
                return $listener->remove($event);
            }
        );

        return $service;
    }
}
