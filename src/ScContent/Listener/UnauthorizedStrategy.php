<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013-2014 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Listener;

use ScContent\Exception\IoCException,
    //
    BjyAuthorize\Exception\UnAuthorizedException,
    BjyAuthorize\Guard\Controller,
    BjyAuthorize\Guard\Route,
    //
    Zend\Stdlib\ResponseInterface as Response,
    Zend\Http\Response as HttpResponse,
    Zend\View\Model\ViewModel,
    Zend\Mvc\Application,
    Zend\Authentication\AuthenticationService,
    Zend\EventManager\AbstractListenerAggregate,
    Zend\EventManager\EventManagerInterface,
    Zend\Mvc\MvcEvent;

class UnauthorizedStrategy extends AbstractListenerAggregate
{
    /**
     * @var Zend\Authentication\AuthenticationService
     */
    protected $authService;

    /**
     * @var string
     */
    protected $redirectRoute = 'zfcuser/login';

    /**
     * @var string
     */
    protected $redirectUrl;

    /**
     * @var string
     */
    protected $template = 'sc-default/template/frontend/user/deny';

    /**
     * @param Zend\Authentication\AuthenticationService $service
     * @return void
     */
    public function setAuthService(AuthenticationService $service)
    {
        $this->authService = $service;
    }

    /**
     * @throws ScContent\Exception\IoCException
     * @return Zend\Authentication\AuthenticationService
     */
    public function getAuthService()
    {
        if (! $this->authService instanceof AuthenticationService) {
            throw new IoCException(
                'The authentication service was not set.'
            );
        }
        return $this->authService;
    }

    /**
     * @param string $route
     * @return void
     */
    public function setRedirectRoute($route)
    {
        $this->redirectRoute = (string) $route;
    }

    /**
     * @return string
     */
    public function getRedirectRoute()
    {
        return $this->redirectRoute;
    }

    /**
     * @param string $url
     * @return void
     */
    public function setRedirectUrl($url)
    {
        $this->redirectUri = $url ? (string) $url : null;
    }

    /**
     * @return string
     */
    public function getRedirectUrl()
    {
        return $this->redirectUrl;
    }

    /**
     * @param string $template
     * @return void
     */
    public function setTemplate($template)
    {
        $this->template = $template;
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * @param Zend\EventManager\EventManagerInterface
     * @return void
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(
            MvcEvent::EVENT_DISPATCH_ERROR,
            [$this, 'onDispatchError'],
            -5000
        );
    }

    /**
     * @param Zend\Mvc\MvcEvent $event
     * @return void
     */
    public function onDispatchError(MvcEvent $event)
    {
        $authService = $this->getAuthService();
        if ($authService->hasIdentity()) {
            $this->performRendering($event);
            return;
        }
        $this->performRedirect($event);
    }

    /**
     * @param Zend\Mvc\MvcEvent $event
     * @return void
     */
    protected function performRedirect(MvcEvent $event)
    {
        // Do nothing if the result is a response object
        $result     = $event->getResult();
        $routeMatch = $event->getRouteMatch();
        $response   = $event->getResponse();
        $request    = $event->getRequest();
        $router     = $event->getRouter();
        $error      = $event->getError();
        $url        = $this->getRedirectUrl();

        if ($result instanceof HttpResponse
            || ! $routeMatch
            || ($response && ! $response instanceof HttpResponse)
            || ! (
                    Route::ERROR === $error
                    || Controller::ERROR === $error
                    || (
                           Application::ERROR_EXCEPTION === $error
                           && ($event->getParam('exception') instanceof UnAuthorizedException)
                       )
                )
        ) {
            return;
        }

        $fallback = substr($request->getUri()->getQuery(), 5);

        if (null === $url) {
            $url = $router->assemble(
                [],
                ['name' => $this->getRedirectRoute(), 'query' => ['redirect' => $fallback]]
            );
        }

        $response = $response ?: new HttpResponse();

        $response->getHeaders()->addHeaderLine('Location', $url);
        $response->setStatusCode(302);

        $event->setResponse($response);
    }

    /**
     * @param Zend\Mvc\MvcEvent $event
     * @return void
     */
    protected function performRendering(MvcEvent $event)
    {
        // Do nothing if the result is a response object
        $result   = $event->getResult();
        $response = $event->getResponse();

        if ($result instanceof Response
            || ($response && ! $response instanceof HttpResponse)
        ) {
            return;
        }

        // Common view variables
        $viewVariables = [
            'error'    => $event->getParam('error'),
            'identity' => $event->getParam('identity'),
        ];

        switch ($event->getError()) {
        	case Controller::ERROR:
        	    $viewVariables['controller'] = $event->getParam('controller');
        	    $viewVariables['action']     = $event->getParam('action');
        	    break;
        	case Route::ERROR:
        	    $viewVariables['route'] = $event->getParam('route');
        	    break;
        	case Application::ERROR_EXCEPTION:
        	    if (!($event->getParam('exception') instanceof UnAuthorizedException)) {
        	        return;
        	    }

        	    $viewVariables['reason'] = $event->getParam('exception')->getMessage();
        	    $viewVariables['error']  = 'error-unauthorized';
        	    break;
        	default:
        	    /*
        	     * do nothing if there is no error in the event or the error
        	     * does not match one of our predefined errors (we don't want
        	     * our 403 template to handle other types of errors)
        	     */

        	    return;
        }

        $model    = new ViewModel($viewVariables);
        $response = $response ?: new HttpResponse();

        $model->setTemplate($this->getTemplate());
        $event->getViewModel()->addChild($model);
        $response->setStatusCode(403);
        $event->setResponse($response);
    }
}
