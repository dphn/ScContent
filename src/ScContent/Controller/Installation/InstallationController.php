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
    ScContent\Service\Installation\AbstractInstallationService,
    ScContent\Exception\DomainException,
    //
    Zend\View\Model\ViewModel;
use ScContent\Service\AbstractService;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class InstallationController extends AbstractInstallation
{
    /**
     * Runs services specified in the configuration.
     *
     * @throws \ScContent\Exception\DomainException
     * @return \Zend\Stdlib\ResponseInterface|\Zend\View\Model\ViewModel
     */
    public function indexAction()
    {
        // To begin the step of installation, the user must click "continue".
        if (! $this->params()->fromRoute('process')) {
            return new ViewModel();
        }

        $installation = $this->getInstallation();
        $redirect     = $this->getRedirect();
        $step         = $installation->getCurrentStep();
        $member       = $step->getCurrentMember();
        $service      = $this->getService();
        $validator    = $this->getValidator();

        foreach ($member as $item) {
            if (! $validator->isValid($item)) {
                if (! $service->process($item)) {
                    return new ViewModel([
                        'errors' => $service->getMessages()
                    ]);
                }
            }
        }
        return $this->redirect()->toUrl($redirect);
    }
}
