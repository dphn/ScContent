<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013-2014 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Controller;

use ScContent\Service\Installation\AbstractInstallationService,
    ScContent\Options\Installation\Installation,
    ScContent\Exception\DomainException,
    //
    Zend\Mvc\Controller\AbstractActionController,
    Zend\Validator\ValidatorInterface,
    Zend\Mvc\MvcEvent;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
abstract class AbstractInstallation extends AbstractActionController
{
    /**
     * @var \ScContent\Options\Installation\Installation
     */
    protected $_installation;

    /**
     * @var \Zend\Validator\ValidatorInterface
     */
    protected $_validator;

    /**
     * @var \ScContent\Service\Installation\AbstractInstallationService
     */
    protected $_service;

    /**
     * @param  \ScContent\Options\Installation\Installation $installation
     * @return void
     */
    public function setInstallation(Installation $installation)
    {
        $this->_installation = $installation;
    }

    /**
     * @throws \ScContent\Exception\DomainException
     * @return \ScContent\Options\Installation\Installation
     */
    public function getInstallation()
    {
        if (! $this->_installation instanceof Installation) {
            $event = $this->getEvent();
            $installation = $event->getParam('installation');
            if (is_null($installation)) {
                throw new DomainException(
                    "Missing event parameter 'installation'."
                );
            }
            if (! $installation instanceof Installation) {
                throw new DomainException(
                    "The event parameter 'installation' must be a instance of 'ScContent\Options\Installation\Installation'."
                );
            }
            $this->_installation = $installation;
        }
        return $this->_installation;
    }

    /**
     * @return string
     */
    public function getRedirect()
    {
        $installation = $this->getInstallation();
        return $this->url()->fromRoute($installation->getRedirectOnSuccess());
    }

    /**
     * @param  \Zend\Validator\ValidatorInterface $validator
     * @return void
     */
    public function setValidator(ValidatorInterface $validator)
    {
        $this->_validator = $validator;
    }

    /**
     * @return \Zend\Validator\ValidatorInterface
     */
    public function getValidator()
    {
        if (! $this->_validator instanceof ValidatorInterface) {
            $installation  = $this->getInstallation();
            $step          = $installation->getCurrentStep();
            $member        = $step->getCurrentMember();
            $validatorName = $member->getValidator();

            $validatorManager = $this->getServiceLocator()->get(
                'ValidatorManager'
            );
            $this->_validator = $validatorManager->get($validatorName);
        }
        return $this->_validator;
    }

    /**
     * @param  \ScContent\Service\Installation\AbstractInstallationService $service
     * @return void
     */
    public function setService(AbstractInstallationService $service)
    {
        $this->_service = $service;
    }

    /**
     * @throws \ScContent\Exception\DomainException
     * @return \ScContent\Service\Installation\AbstractInstallationService
     */
    public function getService()
    {
        if (! $this->_service instanceof AbstractInstallationService) {
            $installation   = $this->getInstallation();
            $step           = $installation->getCurrentStep();
            $member         = $step->getCurrentMember();
            $serviceName    = $member->getService();
            $serviceLocator = $this->getServiceLocator();
            $service        = $serviceLocator->get($serviceName);

            if (! $service instanceof AbstractInstallationService) {
                throw new DomainException(sprintf(
                    $this->scTranslate(
                        "An error occurred while installing the module '%s', step '%s', member '%s'. "
                        . "Service '%s' must inherit from '\ScContent\Service\Installation\AbstractInstallationService'."
                    ),
                    $installation->getModuleName(),
                    $step->getName(),
                    $member->getName(),
                    $serviceName
                ));
            }
            $this->_service = $service;
        }
        return $this->_service;
    }
}
