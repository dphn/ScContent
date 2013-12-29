<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Factory\Form\Installation;

use ScContent\Form\Installation\AccountForm,
    //
    Zend\Validator\Db\NoRecordExists,
    Zend\ServiceManager\ServiceLocatorInterface,
    Zend\ServiceManager\FactoryInterface;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class AccountFormFactory implements FactoryInterface
{
    /**
     * @param Zend\ServiceManager\ServiceLocatorInterface $formElementManager
     * @return ScContent\Form\Installation\AccountForm
     */
    public function createService(ServiceLocatorInterface $formElementManager)
    {
        $serviceLocator = $formElementManager->getServiceLocator();
        $adapter = $serviceLocator->get('ScDb.Adapter');

        $usernameValidator = new NoRecordExists([
            'adapter' => $adapter,
            'table' => 'sc_users',
            'field' => 'username',
            'messages' => [
                NoRecordExists::ERROR_RECORD_FOUND
                    => "Username '%value%' already exists.",
            ],
        ]);

        $emailValidator = new NoRecordExists([
            'adapter' => $adapter,
            'table' => 'sc_users',
            'field' => 'email',
            'messages' => [
                NoRecordExists::ERROR_RECORD_FOUND
                    => "Email '%value%' already exists.",
            ],
        ]);

        $form = new AccountForm($usernameValidator, $emailValidator);
        return $form;
    }
}
