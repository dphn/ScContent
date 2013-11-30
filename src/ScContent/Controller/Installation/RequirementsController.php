<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Controller\Installation;

use ScContent\Controller\AbstractInstallation,
    ScContent\Validator\Installation\Phpini,
    ScContent\Validator\Installation\Phpextension,
    //
    Zend\View\Model\ViewModel;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class RequirementsController extends AbstractInstallation
{
    /**
     * @var ScContent\Validator\Installation\Phpini
     */
    protected $iniValidator;

    /**
     * @var ScContent\Validator\Installation\Phpextension
     */
    protected $extensionValidator;

    /**
     * Checks the settings in the php.ini file in accordance with the
     * configuration of the module. If the settings are not compatible,
     * the installation process stops and displays the information message.
     *
     * @return Zend\Stdlib\ResponseInterface | Zend\View\Model\ViewModel
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
        $options = $this->getModuleOptions()->getInstallation();
        $batch = &$options['steps'][$step]['chain'][$member]['batch'];
        $failures = array();
        $validator = $this->getIniValidator();
        foreach ($batch as &$requirement) {
            if (! $validator->isValid($requirement)) {
                $failures[] = array(
                    $requirement['name'],
                    (false === ini_get($requirement['name']))
                             ? 'false'
                             : ini_get($requirement['name']),
                    $requirement['failure_message']
                );
            }
        }
        if (! empty($failures)) {
            return new ViewModel(array(
                'errors' => array('table' => array(
                    'head' => array('Php.ini option', 'Value', 'Requirement'),
                    'body' => $failures
                ))
            ));
        }
        return $this->redirect()->toUrl($redirect)->setStatusCode(303);
    }

    /**
     * Checks for installed php extensions. If a required php extension
     * is missing, the installation process stops and displays
     * the information message.
     *
     * @return Zend\Stdlib\ResponseInterface | Zend\View\Model\ViewModel
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
        $options = $this->getModuleOptions()->getInstallation();
        $batch = &$options['steps'][$step]['chain'][$member]['batch'];
        $failures = array();
        $validator = $this->getExtensionValidator();
        foreach ($batch as &$requirement) {
            if (! $validator->isValid($requirement)) {
                $failures[] = array(
                    $requirement['name'],
                    $requirement['information'],
                );
            }
        }
        if (! empty($failures)) {
            return new ViewModel(array(
                'errors' => array('table' => array(
                    'head' => array('Missing php extension', 'Information'),
                    'body' => $failures,
                ))
            ));
        }
        return $this->redirect()->toUrl($redirect);
    }

    /**
     * @param ScContent\Validator\Installation\Phpini $validator
     * @return void
     */
    public function setIniValidator(Phpini $validator)
    {
        $this->iniValidator = $validator;
    }

    /**
     * @return ScContent\Validator\Installation\Phpini
     */
    public function getIniValidator()
    {
        if (! $this->iniValidator instanceof Phpini) {
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
     * @param ScContent\Validator\Installation\Phpextension $validator
     * @return void
     */
    public function setExtensionValidator(Phpextension $validator)
    {
        $this->extensionValidator = $validator;
    }

    /**
     * @return ScContent\Validator\Installation\Phpextension
     */
    public function getExtensionValidator()
    {
        if (! $this->extensionValidator instanceof Phpextension) {
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
