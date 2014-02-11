<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013-2014 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Listener\Installation;

use ScContent\Listener\AbstractListener,
    ScContent\Exception\IoCException,
    //
    Zend\Authentication\AuthenticationService,
    Zend\Mvc\MvcEvent;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class InstallationGuard extends AbstractListener
{
    /**
     * @const string
     */
    const AuthController = 'ScController.Installation.Authentication';

    /**
     * @const string
     */
    const AuthAction = 'index';

    /**
     * @var Zend\Authentication\AuthenticationService
     */
    protected $auth;

    /**
     * @param Zend\Authentication\AuthenticationService $service
     * @return void
     */
    public function setAuthService(AuthenticationService $service)
    {
        $this->auth = $service;
    }

    /**
     * @throws ScContent\Exception\IoCException
     * @return Zend\Authentication\AuthenticationService
     */
    public function getAuthService()
    {
        if (! $this->auth instanceof AuthenticationService) {
            throw new IoCException(
                'Authentication service was not set.'
            );
        }
        return $this->auth;
    }

    /**
     * @param Zend\Mvc\MvcEvent $event
     * @return void
     */
    public function process(MvcEvent $event)
    {
        $auth = $this->getAuthService();
        if ($auth->hasIdentity()) {
            return;
        }

        $routeMatch = $event->getRouteMatch();
        $routeMatch->setParam('controller', self::AuthController)
            ->setParam('action', self::AuthAction);

        $event->setRouteMatch($routeMatch);
    }
}
