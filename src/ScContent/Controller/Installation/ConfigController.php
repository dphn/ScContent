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
    ScContent\Service\Installation\ConfigService,
    ScContent\Form\Installation\DatabaseForm,
    //
    Zend\View\Model\ViewModel;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class ConfigController extends AbstractInstallation
{
    /**
     * @var ScContent\Service\Installation\ConfigService
     */
    protected $configService;

    /**
     * @var ScContent\Form\Installation\DatabaseForm
     */
    protected $form;

    /**
     * @return Zend\Stdlib\ResponseInterface | Zend\View\Model\ViewModel
     */
    public function indexAction()
    {
        $redirect = $this->getRedirect();
        $routeMatch = $this->getEvent()->getRouteMatch();
        if (! $routeMatch->getParam('step')
            || ! $routeMatch->getParam('member')
        ) {
            return $this->redirect()
                ->toUrl($redirect)
                ->setStatusCode(303);
        }

        $view = new ViewModel();
        $step = $routeMatch->getParam('step');
        $member = $routeMatch->getParam('member');
        $options = $this->getInstallationInspector()->getCurrentSetup();
        $batch = $options['steps'][$step]['chain'][$member]['batch'];
        $form = $this->getForm();
        $form->setAttribute(
            'action',
            $this->url()->fromRoute(
                'sc-install', array('process' => 'process')
            )
        );
        $view->form = $form;
        $service = $this->getConfigService();
        $config = $service->getConfig();
        $form->bind($config);
        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());
            if ($form->isValid()) {
                $result = $service->saveConfig($config, $batch);
                if ($result) {
                    return $this->redirect()->toUrl($redirect);
                }
                $view->errors = $service->getMessages();
            }
        }
        return $view;
    }

    /**
     * @param ScContent\Form\Installation\DatabaseForm $form
     * @return void
     */
    public function setForm(DatabaseForm $form)
    {
        $this->form = $form;
    }

    /**
     * @return ScContent\Form\Installation\DatabaseForm
     */
    public function getForm()
    {
        if (! $this->form instanceof DatabaseForm) {
            $formElementManager = $this->getServiceLocator()->get(
                'FormElementManager'
            );
            $this->form = $formElementManager->get(
                'ScForm.Installation.Database'
            );
        }
        return $this->form;
    }

    /**
     * @param ScContent\Service\Installation\ConfigService $service
     * @return void
     */
    public function setConfigService(ConfigService $service)
    {
        $this->configService = $service;
    }

    /**
     * @return ScContent\Service\Installation\ConfigService
     */
    public function getConfigService()
    {
        if (! $this->configService instanceof ConfigService) {
            $serviceLocator = $this->getServiceLocator();
            $this->configService = $serviceLocator->get(
                'ScService.Installation.Config'
            );
        }
        return $this->configService;
    }
}
