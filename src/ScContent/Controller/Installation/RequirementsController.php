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
    ScContent\Exception\InvalidArgumentException,
    //
    Zend\Validator\ValidatorInterface,
    Zend\View\Model\ViewModel;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class RequirementsController extends AbstractInstallation
{
    /**
     * @var \Zend\Validator\ValidatorInterface
     */
    protected $validator;

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

        $failures = [];
        foreach ($member as $requirement) {
            if (! $validator->isValid($requirement)) {
                if (! isset($requirement['failure_message'])) {
                    throw new InvalidArgumentException(sprintf(
                        $this->scTranslate(
                            "Unable to verify the  php.ini."
                            ." Missing option 'failure_message' for member '%s'."
                        ),
                        $member->getName()
                    ));
                }
                $failures[] = [
                    $requirement['name'],
                    (false === ini_get($requirement['name']))
                             ? 'false'
                             : ini_get($requirement['name']),
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
        $redirect     = $this->getRedirect();
        $installation = $this->getInstallation();
        $step         = $installation->getCurrentStep();
        $member       = $step->getCurrentMember();
        $validator    = $this->getValidator();

        $failures = [];
        foreach ($member as $requirement) {
            if (! $validator->isValid($requirement)) {
                if (! isset($requirement['information'])) {
                    throw new InvalidArgumentException(sprintf(
                        $this->scTranslate(
                            "Unable to check whether the extension is loaded."
                            ." Missing option 'information' for '%s' param."
                        ),
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
