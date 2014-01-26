<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013-2014 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Listener\Theme;

use ScContent\Controller,
    //
    Zend\EventManager\SharedEventManagerInterface,
    Zend\EventManager\SharedListenerAggregateInterface,
    Zend\Mvc\Application,
    Zend\Mvc\MvcEvent;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class ThemeContext implements SharedListenerAggregateInterface
{
    /**
     * @var Zend\Stdlib\DispatchableInterface
     */
    protected $target;

    /**
     * @param Zend\EventManager\SharedEventManagerInterface $events
     * @return void
     */
    public function attachShared(SharedEventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(
            'Zend\Stdlib\DispatchableInterface',
            'dispatch',
            [$this, 'captureTarget'],
            100
        );

        $this->listeners[] = $events->attach(
            'Zend\Stdlib\DispatchableInterface',
            MvcEvent::EVENT_DISPATCH,
            [$this, 'onDispatch'],
            -85
        );

        $this->listeners[] = $events->attach(
            'Zend\Mvc\Application',
            [MvcEvent::EVENT_DISPATCH_ERROR, MvcEvent::EVENT_RENDER_ERROR],
            [$this, 'onError'],
            100
        );
    }

    /**
     * @param Zend\EventManager\SharedEventManagerInterface $events
     * @return void
     */
    public function detachShared(SharedEventManagerInterface $events)
    {
        foreach ($this->listeners as $index => $listener) {
            if ($events->detach($listener)) {
                unset($this->listeners[$index]);
            }
        }
    }

    /**
     * @param Zend\Mvc\MvcEvent $event
     * @return void
     */
    public function captureTarget(MvcEvent $event) {
        if (! $this->target) {
            $this->target = $event->getTarget();
        }
    }

    /**
     * @param Zend\Mvc\MvcEvent $event
     * @return void
     */
    public function onDispatch(MvcEvent $event)
    {
        $response = $event->getResponse();
        if (404 == $response->getStatusCode()) {
            $this->notFound($event);
            return;
        }

        if ($event->getTarget() !== $this->target) {
            return;
        }

        $application = $event->getApplication();
        $serviceLocator = $application->getServiceManager();

        switch (true) {
            case $this->target instanceof Controller\AbstractInstallation:
                $serviceLocator->get('ScListener.Theme.InstallationStrategy')
                    ->update($event);
                break;
            case $this->target instanceof Controller\AbstractBack:
                $serviceLocator->get('ScListener.Theme.BackendStrategy')
                    ->update($event);
                break;
            case $this->target instanceof Controller\AbstractFront:
                $serviceLocator->get('ScListener.Theme.FrontendStrategy')
                    ->update($event);
                break;
            default:
                $serviceLocator->get('ScListener.Theme.CommonStrategy')
                    ->update($event);
                break;
        }
    }

    /**
     * @param Zend\Mvc\MvcEvent $event
     * @return void
     */
    public function onError(MvcEvent $event)
    {
        $error = $event->getError();
        switch ($error) {
            case Application::ERROR_CONTROLLER_NOT_FOUND:
            case Application::ERROR_CONTROLLER_INVALID:
            case Application::ERROR_ROUTER_NO_MATCH:
                $this->notFound($event);
                return;
        }

        $application    = $event->getApplication();
        $serviceLocator = $application->getServiceManager();

        $viewManager       = $serviceLocator->get('ViewManager');
        $exceptionStrategy = $viewManager->getExceptionStrategy();
        $renderer          = $viewManager->getRenderer();
        $options           = $serviceLocator->get('ScOptions.ModuleOptions');

        $layout   = 'sc-default/layout/{side}/index';
        $template = 'sc-default/template/error/index';
        $side     = 'frontend';
        $theme    = [];

        switch (true) {
            case $this->target instanceof Controller\AbstractBack:
                $side  = 'backend';
                $theme = $options->getBackendTheme();
                break;
            case $this->target instanceof Controller\AbstractInstallation:
                $side  = 'installation';
                $theme = $options->getBackendTheme();
                break;
            case $this->target instanceof Controller\AbstractFront:
            default:
                $theme = $options->getFrontendTheme();
                if (isset($renderer->layout()->regions)) {
                    unset($renderer->layout()->regions);
                }
                break;
        }
        if (isset($theme['errors']['template']['exception'])) {
            $template = $theme['errors']['template']['exception'];
        }
        if (isset($theme['errors']['layout'])) {
            $layout = $theme['errors']['layout'];
        }
        $template = str_replace('{side}', $side, $template);
        $layout   = str_replace('{side}', $side, $layout);
        $exceptionStrategy->setExceptionTemplate($template);
        $renderer->layout()->setTemplate($layout);
    }

    /**
     * @param Zend\Mvc\MvcEvent $event
     * @return void
     */
    public function notFound(MvcEvent $event)
    {
        $application    = $event->getApplication();
        $serviceLocator = $application->getServiceManager();

        $viewManager      = $serviceLocator->get('ViewManager');
        $notFoundStrategy = $viewManager->getRouteNotFoundStrategy();
        $renderer         = $viewManager->getRenderer();
        $options          = $serviceLocator->get('ScOptions.ModuleOptions');

        $layout   = 'sc-default/layout/{side}/index';
        $template = 'sc-default/template/error/404';
        $side     = 'frontend';
        $theme    = [];

        switch (true) {
            case $this->target instanceof Controller\AbstractBack:
                $side  = 'backend';
                $theme = $options->getBackendTheme();
                break;
            case $this->target instanceof Controller\AbstractInstallation:
                $side  = 'installation';
                $theme = $options->getBackendTheme();
                break;
            case $this->target instanceof Controller\AbstractFront:
            default:
                $theme = $options->getFrontendTheme();
                if (isset($renderer->layout()->regions)) {
                    unset($renderer->layout()->regions);
                }
                break;
        }
        if (isset($theme['errors']['template']['404'])) {
            $template = $theme['errors']['template']['404'];
        }
        if (isset($theme['errors']['layout'])) {
            $layout = $theme['errors']['layout'];
        }
        $template = str_replace('{side}', $side, $template);
        $layout   = str_replace('{side}', $side, $layout);
        $notFoundStrategy->setNotFoundTemplate($template);
        $renderer->layout()->setTemplate($layout);
    }
}
