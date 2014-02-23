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
    ScContent\Service\Installation\AuthenticationAdapter,
    ScContent\Mapper\Installation\CredentialsMapper,
    ScContent\Form\Installation\RegistrationForm,
    ScContent\Form\Installation\LoginForm,
    ScContent\Entity\Installation\Credentials,
    //
    Zend\Authentication\AuthenticationService,
    Zend\Crypt\Password\Bcrypt,
    Zend\View\Model\ViewModel;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class AuthenticationController extends AbstractInstallation
{
    /**
     * @var \Zend\Authentication\AuthenticationService
     */
    protected $authService;

    /**
     * @var \ScContent\Mapper\Installation\CredentialsMapper
     */
    protected $mapper;

    /**
     * @var \ScContent\Form\Installation\RegistrationForm
     */
    protected $registrationForm;

    /**
     * @var \ScContent\Form\Installation\LoginForm
     */
    protected $loginForm;

    /**
     * @var \ScContent\Entity\Installation\Credentials
     */
    protected $credentials;


    /**
     * @return \Zend\Stdlib\ResponseInterface|\Zend\View\Model\ViewModel
     */
    public function indexAction()
    {
        $event      = $this->getEvent();
        $routeMatch = $event->getRouteMatch();
        $redirect   = $routeMatch->getParam('redirect', '/');

        $authService = $this->getAuthService();
        if ($authService->hasIdentity()) {
            return $this->redirect()->toUrl($redirect);
        }
        $mapper = $this->getMapper();
        $credentials = $mapper->findCredentials();

        if (empty($credentials)) {
            return $this->registerAction();
        }
        return $this->loginAction();
    }

    /**
     * @return \Zend\Stdlib\ResponseInterface|\Zend\View\Model\ViewModel
     */
    public function loginAction()
    {
        $event      = $this->getEvent();
        $routeMatch = $event->getRouteMatch();
        $redirect   = $routeMatch->getParam('redirect', '/');

        $auth = $this->getAuthService();
        if ($auth->hasIdentity()) {
            return $this->redirect()
                ->toUrl($redirect)
                ->setStatusCode(303);
        }

        $form = $this->getLoginForm();
        $credentials = $this->getCredentials();
        $form->bind($credentials);

        $view = new ViewModel([
            'form'   => $form,
            'step'   => false,
            'title'  => 'Installer Protection',
            'header' => 'Installer Protection.',
            'info'   => 'The system detects that you need to install some compo'
                      . 'nents. To begin the installation enter your credentials.',
        ]);

        if ($this->getRequest()->isPost()) {
            $form->setData($this->params()->fromPost());
            if ($form->isValid()) {
                $result = $auth->authenticate();
                if ($result->isValid()) {
                    return $this->redirect()->toUrl($redirect);
                }
                $view->info = 'Credentials are not correct.';
            }
        }

        return $view;
    }

    /**
     * @return \Zend\Stdlib\ResponseInterface|\Zend\View\Model\ViewModel
     */
    public function registerAction()
    {
        $event      = $this->getEvent();
        $routeMatch = $event->getRouteMatch();
        $redirect   = $routeMatch->getParam('redirect', '/');

        $mapper = $this->getMapper();
        $data = $mapper->findCredentials();
        if (! empty($data)) {
            return $this->redirect()
                ->toUrl($redirect)
                ->setStatusCode(303);
        }

        $form = $this->getRegistrationForm();
        $credentials = $this->getCredentials();
        $form->bind($credentials);

        if ($this->getRequest()->isPost()) {
            $form->setData($this->params()->fromPost());
            if ($form->isValid()) {
                $bcrypt = new Bcrypt();
                $bcrypt->setCost(AuthenticationAdapter::BcryptCost);
                $credentials->setUsername($bcrypt->create($credentials->getUsername()));
                $credentials->setPassword($bcrypt->create($credentials->getPassword()));
                $mapper->save($credentials);

                $this->getAuthService()->authenticate();

                return $this->redirect()->toUrl($redirect);
            }
        }
        return new ViewModel([
            'form'   => $form,
            'step'   => false,
            'title'  => 'Installer Protection',
            'header' => 'Installer Protection.',
            'info'   => 'The installer uses its own defense, which is triggered'
                      . ' by the partial or complete failure of the main system'
                      . '. Please, protect the installer with a password.',
        ]);
    }

    /**
     * @param  \Zend\Authentication\AuthenticationService $service
     * @return void
     */
    public function setAuthService(AuthenticationService $service)
    {
        $this->authService = $service;
    }

    /**
     * @return \Zend\Authentication\AuthenticationService
     */
    public function getAuthService()
    {
        if (! $this->authService instanceof AuthenticationService) {
            $serviceLocator = $this->getServiceLocator();
            $this->authService = $serviceLocator->get(
                'ScService.Installation.AuthService'
            );
        }
        return $this->authService;
    }

    /**
     * @param  \ScContent\Mapper\Installation\CredentialsMapper $mapper
     * @return void
     */
    public function setMapper(CredentialsMapper $mapper)
    {
        $this->mapper = $mapper;
    }

    /**
     * @return \ScContent\Mapper\Installation\CredentialsMapper
     */
    public function getMapper()
    {
        if (! $this->mapper instanceof CredentialsMapper) {
            $serviceLocator = $this->getServiceLocator();
            $this->mapper = $serviceLocator->get(
                'ScMapper.Installation.Credentials'
            );
        }
        return $this->mapper;
    }

    /**
     * @param  \ScContent\Form\Installation\RegistrationForm $form
     * @return void
     */
    public function setRegistrationForm(RegistrationForm $form)
    {
        $this->registrationForm = $form;
    }

    /**
     * @return \ScContent\Form\Installation\RegistrationForm
     */
    public function getRegistrationForm()
    {
        if (! $this->registrationForm instanceof RegistrationForm) {
            $this->registrationForm = new RegistrationForm();
        }
        return $this->registrationForm;
    }

    /**
     * @param  \ScContent\Form\Installation\LoginForm $form
     * @return void
     */
    public function setLoginForm(LoginForm $form)
    {
        $this->loginForm = $form;
    }

    /**
     * @return \ScContent\Form\Installation\LoginForm
     */
    public function getLoginForm()
    {
        if (! $this->loginForm instanceof LoginForm) {
            $this->loginForm = new LoginForm();
        }
        return $this->loginForm;
    }

    /**
     * @param  \ScContent\Entity\Installation\Credentials $entity
     * @return void
     */
    public function setCredentials(Credentials $entity)
    {
        $this->credentials = $entity;
    }

    /**
     * @return \ScContent\Entity\Installation\Credentials
     */
    public function getCredentials()
    {
        if (! $this->credentials instanceof Credentials) {
            $this->credentials = new Credentials();
        }
        return $this->credentials;
    }
}
