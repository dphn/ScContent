<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013-2014 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Factory\View\Helper;

use ScContent\View\Helper\DateTime,
    //
    Zend\ServiceManager\ServiceLocatorInterface,
    Zend\ServiceManager\FactoryInterface;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class DateTimeFactory implements FactoryInterface
{
    /**
     * @param  \Zend\ServiceManager\ServiceLocatorInterface $viewHelperPluginManager
     * @return \ScContent\View\Helper\DateTime
     */
    public function createService(
        ServiceLocatorInterface $viewHelperPluginManager
    ) {
        $serviceLocator = $viewHelperPluginManager->getServiceLocator();
        $scDatetime = $serviceLocator->get('ScService.DateTime');
        $dateformat = $viewHelperPluginManager->get('dateformat');
        $viewHelper = new DateTime($scDatetime, $dateformat);
        return $viewHelper;
    }
}
