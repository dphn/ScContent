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
    ScContent\Validator\Installation\PhpExtension,
    ScContent\Validator\Installation\PhpIni,
    ScContent\Exception\InvalidArgumentException,
    ScContent\Exception\DomainException,
    //
    Zend\Validator\ValidatorInterface,
    Zend\View\Model\ViewModel;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class RequirementsController extends AbstractInstallation
{
    /**
     * Checks the settings in the php.ini file in accordance with the
     * configuration of the module. If the settings are not compatible,
     * the installation process stops and displays the information message.
     *
     * @return \Zend\Stdlib\ResponseInterface|\Zend\View\Model\ViewModel
     */
    public function configurationAction()
    {
        $redirect     = $this->getRedirect();
        $installation = $this->getInstallation();
        $step         = $installation->getCurrentStep();
        $member       = $step->getCurrentMember();
        $validator    = $this->getValidator();

        if (! $validator instanceof PhpIni) {
            throw new DomainException(sprintf(
                $this->scTranslate(
                    "An error occurred while installing the module '%s', step '%s', member '%s'. "
                    . "Validator must be an instance of class '%s', '%s' instead."
                ),
                $installation->getModuleName(),
                $step->getName(),
                $member->getName(),
                'ScContent\Validator\Installation\PhpIni',
                is_object($validator) ? get_class($validator) : gettype($validator)
            ));
        }

        $failures = [];
        foreach ($member as $requirement) {
            if (! $validator->isValid($requirement)) {
                if (! isset($requirement['failure_message'])) {
                    throw new InvalidArgumentException(sprintf(
                        $this->scTranslate(
                            "An error occurred while installing the module '%s', step '%s', member '%s'. "
                            . "Missing option 'failure_message' for '%s' param."
                        ),
                        $installation->getModuleName(),
                        $step->getName(),
                        $member->getName(),
                        $requirement['name']
                    ));
                }
                $failures[] = [
                    $requirement['name'],
                    (false === $validator->getValueFromCallback($requirement['name']))
                             ? 'false'
                             : $validator->getValueFromCallback($requirement['name']),
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
     * is not installed, the installation process stops and displays
     * the information message.
     *
     * @return \Zend\Stdlib\ResponseInterface|\Zend\View\Model\ViewModel
     */
    public function extensionAction()
    {
        $redirect     = $this->getRedirect();
        $installation = $this->getInstallation();
        $step         = $installation->getCurrentStep();
        $member       = $step->getCurrentMember();
        $validator    = $this->getValidator();

        if (! $validator instanceof PhpExtension) {
            throw new DomainException(sprintf(
                $this->scTranslate(
                    "An error occurred while installing the module '%s', step '%s', member '%s'. "
                    . "Validator must be an instance of class '%s', '%s' instead."
                ),
                $installation->getModuleName(),
                $step->getName(),
                $member->getName(),
                'ScContent\Validator\Installation\PhpExtension',
                is_object($validator) ? get_class($validator) : gettype($validator)
            ));
        }

        $failures = [];
        foreach ($member as $requirement) {
            if (! $validator->isValid($requirement)) {
                if (! isset($requirement['information'])) {
                    throw new InvalidArgumentException(sprintf(
                        $this->scTranslate(
                            "An error occurred while installing the module '%s', step '%s', member '%s'. "
                            . "Missing option 'information' for '%s' param."
                        ),
                        $installation->getModuleName(),
                        $step->getName(),
                        $member->getName(),
                        $requirement['name']
                    ));
                }
                $failures[] = [
                    $requirement['name'],
                    $requirement['information'],
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
}
