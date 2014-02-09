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
    ScContent\Service\Installation\AccountService,
    ScContent\Form\Installation\AccountForm,
    //
    Zend\Stdlib\Hydrator\ClassMethods,
    Zend\View\Model\ViewModel;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class AccountController extends AbstractInstallation
{
    /**
     * @var ScContent\Service\Installation\AccountService
     */
    protected $service;

    /**
     * @var ScContent\Form\Installation\AccountForm
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
        $form = $this->getAccountForm();
        $form->setHydrator(new ClassMethods());
        $form->setAttribute(
            'action',
            $this->url()->fromRoute(
                'sc-install', ['process' => 'process']
            )
        );
        $view->form = $form;
        $service = $this->getAccountService();
        $user = $service->makeUserEntity();
        $form->bind($user);
        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());
            if ($form->isValid()) {
                $service->createAccount($user);
                return $this->redirect()->toUrl($redirect);
            }
        }
        return $view;
    }

    /**
     * @param ScContent\Service\Installation\AccountService $service
     * @return void
     */
    public function setAccountService(AccountService $service)
    {
        $this->service = $service;
    }

    /**
     * @return ScContent\Service\Installation\AccountService
     */
    public function getAccountService()
    {
        if (! $this->service instanceof AccountService) {
            $serviceLocator = $this->getServiceLocator();
            $this->service = $serviceLocator->get(
                'ScService.Installation.Account'
            );
        }
        return $this->service;
    }

    /**
     * @param ScContent\Form\Installation\AccountForm $form
     * @return void
     */
    public function setAccountForm(AccountForm $form)
    {
        $this->form = $form;
    }

    /**
     * @return ScContent\Form\Installation\AccountForm
     */
    public function getAccountForm()
    {
        if (! $this->form instanceof AccountForm) {
            $serviceLocator = $this->getServiceLocator();
            $formElementManager = $serviceLocator->get(
                'FormElementManager'
            );
            $this->form = $formElementManager->get(
                'ScForm.Installation.Account'
            );
        }
        return $this->form;
    }
}
