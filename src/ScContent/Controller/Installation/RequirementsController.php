<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013-2014 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Controller\Installation;

use ScContent\Controller\AbstractInstallation,
    ScContent\Validator\Installation\PhpIni,
    ScContent\Validator\Installation\PhpExtension,
    ScContent\Exception\InvalidArgumentException,
    //
    Zend\View\Model\ViewModel;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class RequirementsController extends AbstractInstallation
{
    /**
     * @var \ScContent\Validator\Installation\PhpIni
     */
    protected $iniValidator;

    /**
     * @var \ScContent\Validator\Installation\PhpExtension
     */
    protected $extensionValidator;

    /**
     * Checks the settings in the php.ini file in accordance with the
     * configuration of the module. If the settings are not compatible,
     * the installation process stops and displays the information message.
     *
     * @return \Zend\Stdlib\ResponseInterface|\Zend\View\Model\ViewModel
     */
    public function configurationAction()
    {
        // To begin the step of installation, the user must click "continue".
        if (! $this->params()->fromRoute('process')) {
            return new ViewModel();
        }
        $redirect = $this->getRedirect();
        $routeMatch = $this->getEvent()->getRouteMatch();
        if (! $routeMatch->getParam('step')
            || ! $routeMatch->getParam('member')
        ) {
            return $this->redirect()->toUrl($redirect)->setStatusCode(303);
        }
        $step = $routeMatch->getParam('step');
        $member = $routeMatch->getParam('member');
        $options = $this->getInstallationInspector()->getCurrentSetup();
        if (! isset($options['steps'][$step]['chain'][$member]['batch'])) {
            return $this->redirect()->toUrl($redirect)->setStatusCode(303);
        }
        $batch = &$options['steps'][$step]['chain'][$member]['batch'];
        $failures = [];
        $validator = $this->getIniValidator();
        if (isset($batch['items']) && is_array($batch['items'])) {
            foreach ($batch['items'] as &$requirement) {
                if (! isset($requirement['name'])) {
                    throw new InvalidArgumentException($this->scTranslate(
                        "Unable to verify the  php.ini."
                        ." Missing option 'name' in the installer configuration."
                    ));
                }
                if (! isset($requirement['failure_message'])) {
                    throw new InvalidArgumentException(sprintf(
                        $this->scTranslate(
                            "Unable to verify the  php.ini."
                            ." Missing option 'failure_message' for '%s' param."
                        ),
                        $requirement['name']
                    ));
                }
                if (! $validator->isValid($requirement)) {
                    $failures[] = [
                        $requirement['name'],
                        (false === ini_get($requirement['name']))
                                 ? 'false'
                                 : ini_get($requirement['name']),
                        $requirement['failure_message']
                    ];
                }
            }
        } elseif (is_array($batch) && ! empty($batch)) {
            if (! isset($batch['name'])) {
                throw new InvalidArgumentException($this->scTranslate(
                    "Unable to verify the  php.ini."
                    ." Missing option 'name' in the installer configuration."
                ));
            }
            if (! isset($batch['failure_message'])) {
                throw new InvalidArgumentException(sprintf(
                    $this->scTranslate(
                        "Unable to verify the  php.ini."
                        ." Missing option 'failure_message' for '%s' param."
                    ),
                    $requirement['name']
                ));
            }
            if (! $validator->isValid($batch)) {
                $failures[] = [
                    $batch['name'],
                    (false === ini_get($batch['name']))
                             ? 'false'
                             : ini_get($batch['name']),
                     $requirement['failure_message']
                ];
            }
        }
        if (! empty($failures)) {
            return new ViewModel([
                'errors' => ['table' => [
                    'head' => ['Php.ini option', 'Value', 'Requirement'],
                    'body' => $failures
                ]]
            ]);
        }
        return $this->redirect()->toUrl($redirect);
    }

    /**
     * Checks for installed php extensions. If a required php extension
     * is missing, the installation process stops and displays
     * the information message.
     *
     * @return \Zend\Stdlib\ResponseInterface|\Zend\View\Model\ViewModel
     */
    public function extensionAction()
    {
        $redirect = $this->getRedirect();
        $routeMatch = $this->getEvent()->getRouteMatch();
        if (! $routeMatch->getParam('step')
            || ! $routeMatch->getParam('member')
        ) {
            return $this->redirect()->toUrl($redirect)->setStatusCode(303);
        }
        $step = $routeMatch->getParam('step');
        $member = $routeMatch->getParam('member');
        $options = $this->getInstallationInspector()->getCurrentSetup();
        if (! isset($options['steps'][$step]['chain'][$member]['batch'])) {
            return $this->redirect()->toUrl($redirect)->setStatusCode(303);
        }
        $batch = &$options['steps'][$step]['chain'][$member]['batch'];
        $failures = [];
        $validator = $this->getExtensionValidator();
        if (isset($batch['items']) && is_array($batch['items'])) {
            foreach ($batch['items'] as &$requirement) {
                if (! isset($requirement['name'])) {
                    throw new InvalidArgumentException($this->scTranslate(
                        "Unable to check whether the extension is loaded."
                        ." Missing option 'name' in the installer configuration."
                    ));
                }
                if (! isset($requirement['information'])) {
                    throw new InvalidArgumentException(sprintf(
                        $this->scTranslate(
                            "Unable to check whether the extension is loaded."
                            ." Missing option 'information' for '%s' param."
                        ),
                        $requirement['name']
                    ));
                }
                if (! $validator->isValid($requirement)) {
                    $failures[] = [
                        $requirement['name'],
                        $requirement['information'],
                    ];
                }
            }
        } elseif (is_array($batch) && ! empty($batch)) {
            if (! isset($batch['name'])) {
                throw new InvalidArgumentException($this->scTranslate(
                    "Unable to check whether the extension is loaded."
                    ." Missing option 'name' in the installer configuration."
                ));
            }
            if (! isset($batch['information'])) {
                throw new InvalidArgumentException(sprintf(
                    $this->scTranslate(
                        "Unable to check whether the extension is loaded."
                        ." Missing option 'information' for '%s' param."
                    ),
                    $requirement['name']
                ));
            }
            if (! $validator->isValid($requirement)) {
                $failures[] = [
                    $batch['name'],
                    $batch['information'],
                ];
            }
        }
        if (! empty($failures)) {
            return new ViewModel([
                'errors' => ['table' => [
                    'head' => ['Missing php extension', 'Information'],
                    'body' => $failures,
                ]]
            ]);
        }
        return $this->redirect()->toUrl($redirect);
    }

    /**
     * @param  \ScContent\Validator\Installation\PhpIni $validator
     * @return void
     */
    public function setIniValidator(Phpini $validator)
    {
        $this->iniValidator = $validator;
    }

    /**
     * @return \ScContent\Validator\Installation\PhpIni
     */
    public function getIniValidator()
    {
        if (! $this->iniValidator instanceof PhpIni) {
            $validatorManager = $this->getServiceLocator()->get(
                'ValidatorManager'
            );
            $this->iniValidator = $validatorManager->get(
                'ScValidator.Installation.PhpIni'
            );
        }
        return $this->iniValidator;
    }

    /**
     * @param  \ScContent\Validator\Installation\PhpExtension $validator
     * @return void
     */
    public function setExtensionValidator(Phpextension $validator)
    {
        $this->extensionValidator = $validator;
    }

    /**
     * @return \ScContent\Validator\Installation\PhpExtension
     */
    public function getExtensionValidator()
    {
        if (! $this->extensionValidator instanceof PhpExtension) {
            $validatorManager = $this->getServiceLocator()->get(
                'ValidatorManager'
            );
            $this->extensionValidator = $validatorManager->get(
                'ScValidator.Installation.PhpExtension'
            );
        }
        return $this->extensionValidator;
    }
}
