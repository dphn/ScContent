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
    Zend\EventManager\AbstractListenerAggregate,
    Zend\EventManager\EventManagerInterface,
    Zend\Mvc\Application,
    Zend\Mvc\MvcEvent;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class ThemeContext extends AbstractListenerAggregate
{
    /**
     * @var Zend\Stdlib\DispatchableInterface
     */
    protected $target;

    /**
     * @param Zend\EventManager\EventManagerInterface $events
     * @return void
     */
    public function attach(EventManagerInterface $events)
    {
        $sharedEvents = $events->getSharedManager();

        $events->attach(
            MvcEvent::EVENT_ROUTE,
            [$this, 'capture'],
            -PHP_INT_MAX
        );

        $sharedEvents->attach(
            'Zend\Stdlib\DispatchableInterface',
            MvcEvent::EVENT_DISPATCH,
            [$this, 'onDispatch'],
            -85
        );

        $this->listeners[] = $events->attach(
            [MvcEvent::EVENT_DISPATCH_ERROR, MvcEvent::EVENT_RENDER_ERROR],
            [$this, 'onError'],
            100
        );
    }

    /**
     * @param Zend\Mvc\MvcEvent $event
     * @return void
     */
    public function capture(MvcEvent $event) {
        $match = $event->getRouteMatch();
        $controller = $match->getParam('controller');

        $application = $event->getApplication();
        $serviceLocator = $application->getServiceManager();
        $controllerLoader = $serviceLocator->get('ControllerLoader');

        if ($controllerLoader->has($controller)) {
            $this->target = $controllerLoader->get($controller);
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

        $application = $event->getApplication();
        $serviceLocator = $application->getServiceManager();

        $viewManager = $serviceLocator->get('ViewManager');
        $renderer = $viewManager->getRenderer();
        $exceptionStrategy = $viewManager->getExceptionStrategy();
        $options  = $serviceLocator->get('ScOptions.ModuleOptions');

        $layout = 'sc-default/layout/{side}/index';
        $template = 'sc-default/template/error/index';

        switch (true) {
            case $this->target instanceof Controller\AbstractBack:
                $theme = $options->getBackendTheme();
                // template
                if (isset($theme['errors']['template']['exception'])) {
                   $template = $theme['errors']['template']['exception'];
                }
                $template = str_replace('{side}', 'backend', $template);
                $exceptionStrategy->setExceptionTemplate($template);
                // layout
                if (isset($theme['errors']['layout'])) {
                   $layout = $theme['errors']['layout'];
                }
                $layout = str_replace('{side}', 'backend', $layout);
                $renderer->layout()->setTemplate($layout);
                break;
            case $this->target instanceof Controller\AbstractInstallation:
                $theme = $options->getBackendTheme();
                // template
                if (isset($theme['errors']['template']['exception'])) {
                   $template = $theme['errors']['template']['exception'];
                }
                $template = str_replace('{side}', 'installation', $template);
                $exceptionStrategy->setExceptionTemplate($template);
                // layout
                if (isset($theme['errors']['layout'])) {
                   $layout = $theme['errors']['layout'];
                }
                $layout = str_replace('{side}', 'installation', $layout);
                $renderer->layout()->setTemplate($layout);
                break;
            case $this->target instanceof Controller\AbstractFront:
            default:
                $theme = $options->getFrontendTheme();
                // template
                if (isset($theme['errors']['template']['exception'])) {
                    $template = $theme['errors']['template']['exception'];
                }
                $template = str_replace('{side}', 'frontend', $template);
                $exceptionStrategy->setExceptionTemplate($template);
                // layout
                if (isset($theme['errors']['layout'])) {
                    $layout = $theme['errors']['layout'];
                }
                $layout = str_replace('{side}', 'frontend', $layout);

                if (isset($renderer->layout()->regions)) {
                    unset($renderer->layout()->regions);
                }

                $renderer->layout()->setTemplate($layout);
                break;
        }
    }

    /**
     * @param Zend\Mvc\MvcEvent $event
     * @return void
     */
    public function notFound(MvcEvent $event)
    {
        $application = $event->getApplication();
        $serviceLocator = $application->getServiceManager();

        $viewManager = $serviceLocator->get('ViewManager');
        $renderer = $viewManager->getRenderer();
        $options  = $serviceLocator->get('ScOptions.ModuleOptions');
        $notFoundStrategy = $viewManager->getRouteNotFoundStrategy();

        $layout = 'sc-default/layout/{side}/index';
        $template = 'sc-default/template/error/404';

        switch (true) {
            case $this->target instanceof Controller\AbstractBack:
                $theme = $options->getBackendTheme();
                // template
                if (isset($theme['errors']['template']['404'])) {
                    $template = $theme['errors']['template']['404'];
                }
                $template = str_replace('{side}', 'backend', $template);
                $notFoundStrategy->setNotFoundTemplate($template);
                // layout
                if (isset($theme['errors']['layout'])) {
                    $layout = $theme['errors']['layout'];
                }
                $layout = str_replace('{side}', 'backend', $layout);
                $renderer->layout()->setTemplate($layout);
                break;
            case $this->target instanceof Controller\AbstractInstallation:
                $theme = $options->getBackendTheme();
                // template
                if (isset($theme['errors']['template']['404'])) {
                    $template = $theme['errors']['template']['404'];
                }
                $template = str_replace('{side}', 'installation', $template);
                $notFoundStrategy->setNotFoundTemplate($template);
                // layout
                if (isset($theme['errors']['layout'])) {
                    $layout = $theme['errors']['layout'];
                }
                $layout = str_replace('{side}', 'installation', $layout);
                $renderer->layout()->setTemplate($layout);
                break;
            case $this->target instanceof Controller\AbstractFront:
            default:
                $theme = $options->getFrontendTheme();
                // template
                if (isset($theme['errors']['template']['404'])) {
                    $template = $theme['errors']['template']['404'];
                }
                $template = str_replace('{side}', 'frontend', $template);
                $notFoundStrategy->setNotFoundTemplate($template);
                // layout
                if (isset($theme['errors']['layout'])) {
                    $layout = $theme['errors']['layout'];
                }
                $layout = str_replace('{side}', 'frontend', $layout);

                if (isset($renderer->layout()->regions)) {
                    unset($renderer->layout()->regions);
                }
                $renderer->layout()->setTemplate($layout);
                break;
        }
    }
}
