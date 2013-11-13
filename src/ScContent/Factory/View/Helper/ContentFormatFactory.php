<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Factory\View\Helper;

use ScContent\View\Helper\ContentFormat,
    //
    Zend\ServiceManager\ServiceLocatorInterface,
    Zend\ServiceManager\FactoryInterface;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class ContentFormatFactory implements FactoryInterface
{
    /**
     * @param Zend\ServiceManager\ServiceLocatorInterface $viewHelperPluginManager
     * @return ScContent\View\Helper\ContentFormat
     */
    public function createService(
        ServiceLocatorInterface $viewHelperPluginManager
    ) {
        $serviceLocator = $viewHelperPluginManager->getServiceLocator();
        $dir = $serviceLocator->get('sc-service.dir');
        $catalog = $serviceLocator->get('sc-service.file.types.catalog');
        $basePath = $viewHelperPluginManager->get('basepath');
        $viewHelper = new ContentFormat($basePath, $dir, $catalog);
        return $viewHelper;
    }
}
