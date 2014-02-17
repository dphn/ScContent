<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013-2014 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Factory\Form\Installation;

use ScContent\Form\Installation\DatabaseForm,
    //
    Zend\ServiceManager\ServiceLocatorInterface,
    Zend\ServiceManager\FactoryInterface,
    //
    PDO;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class DatabaseFormFactory implements FactoryInterface
{
    /**
     * @param  \Zend\ServiceManager\ServiceLocatorInterface $formElementManager
     * @return \ScContent\Form\Installation\DatabaseForm
     */
    public function createService(ServiceLocatorInterface $formElementManager)
    {
        $drivers = PDO::getAvailableDrivers();
        $form = new DatabaseForm($drivers);
        return $form;
    }
}
