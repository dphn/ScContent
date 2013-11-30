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

use ScContent\Form\Back\FileAddForm,
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
     * @return ScContent\Form\Back\FileAddForm
     */
    public function createService(ServiceLocatorInterface $formElementManager)
    {
        $serviceLocator = $formElementManager->getServiceLocator();
        $validatorManager = $serviceLocator->get('ValidatorManager');
        $fileNameValidator = $validatorManager->get('ScValidator.File.Name');
        $fileTypeValidator = $validatorManager->get('ScValidator.File.Type');

        $filterManager = $serviceLocator->get('FilterManager');
        $mimeTypeFilter = $filterManager->get('ScFilter.File.MimeType');

        $form = new FileAddForm(
            $mimeTypeFilter,
            $fileNameValidator,
            $fileTypeValidator
        );
        return $form;
    }
}
