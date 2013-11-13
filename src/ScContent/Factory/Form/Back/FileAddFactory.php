<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Factory\Form\Back;

use ScContent\Form\Back\FileAdd,
    //
    Zend\ServiceManager\ServiceLocatorInterface,
    Zend\ServiceManager\FactoryInterface;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class FileAddFactory implements FactoryInterface
{
    /**
     * @param Zend\ServiceManager\ServiceLocatorInterface $formElementManager
     * @return ScContent\Form\Back\FileAdd
     */
    public function createService(ServiceLocatorInterface $formElementManager)
    {
        $serviceLocator = $formElementManager->getServiceLocator();
        $validatorManager = $serviceLocator->get('ValidatorManager');
        $fileNameValidator = $validatorManager->get('sc-validator.file.name');
        $fileTypeValidator = $validatorManager->get('sc-validator.file.type');

        $filterManager = $serviceLocator->get('FilterManager');
        $mimeTypeFilter = $filterManager->get('sc-filter.file.mime');

        $form = new FileAdd(
            $mimeTypeFilter,
            $fileNameValidator,
            $fileTypeValidator
        );
        return $form;
    }
}
