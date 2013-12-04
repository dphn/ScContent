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
    //
    Zend\Validator\ValidatorPluginManager,
    Zend\View\Model\ViewModel;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class InstallationController extends AbstractInstallation
{
    /**
     * @var Zend\Validator\ValidatorPluginManager
     */
    protected $validatorManager;

    /**
     * Runs services specified in the configuration.
     *
     * @return Zend\Stdlib\ResponseInterface | Zend\View\Model\ViewModel
     */
    public function indexAction()
    {
        $viewModel = new ViewModel();

        // To begin the step of installation, the user must click "continue".
        if (! $this->params()->fromRoute('process')) {
            return $viewModel;
        }
        $redirect = $this->getRedirect();
        $routeMatch = $this->getEvent()->getRouteMatch();
        if (! $routeMatch->getParam('step')
            || ! $routeMatch->getParam('member')
        ) {
            return $this->redirect()->toUrl($redirect)->setStatusCode(303);
        }
        $isStarted = false;
        $options = $this->getInstallationInspector()->getCurrentSetup();
        $step = &$options['steps'][$routeMatch->getParam('step')];
        foreach ($step['chain'] as $memberName => &$member) {
            if ($memberName == $routeMatch->getParam('member')) {
                $isStarted = true;
            }
            if (! $isStarted) {
                continue;
            }
            if (! isset($member['service'])) {
                continue;
            }
            $validator = $this->getValidatorManager()->get($member['validator']);
            $service = $this->getServiceLocator()->get($member['service']);
            $batch = null;
            if (isset($member['batch'])) {
                $batch = $member['batch'];
            }
            if (isset($batch['items']) && is_array($batch['items'])) {
                foreach ($batch['items'] as $item) {
                    if (! $validator->isValid($item)) {
                        if (! $service->process($item)) {
                            $viewModel->errors = $service->getMessages();
                            return $viewModel;
                        }
                    }
                }
            } else {
                if (! $validator->isValid($batch)) {
                    if (! $service->process($batch)) {
                        $viewModel->errors = $service->getMessages();
                        return $viewModel;
                    }
                }
            }
        }
        return $this->redirect()->toUrl($redirect);
    }

    /**
     * @param Zend\Validator\ValidatorPluginManager
     * @retunr void
     */
    public function setValidatorManager(ValidatorPluginManager $manager)
    {
        $this->validatorManager = $manager;
    }

    /**
     * @return Zend\Validator\ValidatorPluginManager
     */
    public function getValidatorManager()
    {
        if (! $this->validatorManager instanceof ValidatorPluginManager) {
            $serviceLocator = $this->getServiceLocator();
            $this->validatorManager = $serviceLocator->get('ValidatorManager');
        }
        return $this->validatorManager;
    }
}
