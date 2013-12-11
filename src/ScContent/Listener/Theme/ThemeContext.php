<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Listener\Theme;

use ScContent\Controller\AbstractBack,
    ScContent\Controller\AbstractFront,
    ScContent\Controller\AbstractInstallation,
    //
    Zend\EventManager\AbstractListenerAggregate,
    Zend\EventManager\EventManagerInterface,
    Zend\Mvc\MvcEvent;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class ThemeContext extends AbstractListenerAggregate
{
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(
            MvcEvent::EVENT_DISPATCH,
            [$this, 'process'],
            -100
        );
        $this->listeners[] = $events->attach(
            MvcEvent::EVENT_DISPATCH_ERROR,
            [$this, 'error']
        );
    }

    /**
     * @param Zend\Mvc\MvcEvent $event
     * @return void
     */
    public function process(MvcEvent $event)
    {
        $app = $event->getApplication();
        $sm = $app->getServiceManager();

        $response = $event->getResponse();
        if (404 == $response->getStatusCode()) {
            $this->notFound($event);
            return;
        }

        $target = $event->getTarget();

        switch (true) {
            case $target instanceof AbstractBack:
                $strategy = $sm->get('ScListener.Theme.BackendStrategy');
                $strategy->update($event);
                break;
            case $target instanceof AbstractFront:
                $strategy = $sm->get('ScListener.Theme.FrontendStrategy');
                $strategy->update($event);
                break;
            case $target instanceof AbstractInstallation:
                $strategy = $sm->get('ScListener.Theme.InstallationStrategy');
                $strategy->update($event);
                break;
        }
    }

    public function error(MvcEvent $event)
    {
        $target = $event->getTarget();

        $app = $event->getApplication();
        $sm = $app->getServiceManager();
        $strategy = $sm->get('Zend\Mvc\View\ExceptionStrategy');
        $options  = $sm->get('ScOptions.ModuleOptions');

        $layout = 'sc-default/layout/{side}/index';
        $template = 'sc-default/template/error/index';

        switch (true) {
            case $target instanceof AbstractBack:
                $theme = $options->getBackendTheme();
                // template
                if (isset($theme['errors']['template']['exception'])) {
                   $template = $theme['errors']['template']['exception'];
                }
                $template = str_replace('{side}', 'backend', $template);
                $strategy->setExceptionTemplate($template);
                // layout
                if (isset($theme['errors']['layout'])) {
                   $layout = $theme['errors']['layout'];
                }
                $layout = str_replace('{side}', 'backend', $layout);
                $event->getTarget()->layout($layout);
                break;
            case $target instanceof AbstractInstallation:
                $theme = $options->getBackendTheme();
                // template
                if (isset($theme['errors']['template']['exception'])) {
                   $template = $theme['errors']['template']['exception'];
                }
                $template = str_replace('{side}', 'installation', $template);
                $strategy->setExceptionTemplate($template);
                // layout
                if (isset($theme['errors']['layout'])) {
                   $layout = $theme['errors']['layout'];
                }
                $layout = str_replace('{side}', 'installation', $layout);
                $event->getTarget()->layout($layout);
                break;
            case $target instanceof AbstractFront:
                $theme = $options->getFrontendTheme();
                // template
                if (isset($theme['errors']['template']['exception'])) {
                    $template = $theme['errors']['template']['exception'];
                }
                $template = str_replace('{side}', 'frontend', $template);
                $strategy->setExceptionTemplate($template);
                // layout
                if (isset($theme['errors']['layout'])) {
                    $layout = $theme['errors']['layout'];
                }
                $layout = str_replace('{side}', 'frontend', $layout);
                $event->getTarget()->layout($layout);
                break;
        }
    }

    /**
     * @param Zend\Mvc\MvcEvent $event
     * @return void
     */
    protected function notFound(MvcEvent $event)
    {
        $target = $event->getTarget();

        $app = $event->getApplication();
        $sm = $app->getServiceManager();
        $options  = $sm->get('ScOptions.ModuleOptions');
        $listener = $sm->get('404Strategy');

        $layout = 'sc-default/layout/{side}/index';
        $template = 'sc-default/template/error/index';

        switch (true) {
            case $target instanceof AbstractBack:
                $theme = $options->getBackendTheme();
                // template
                if (isset($theme['errors']['template']['404'])) {
                    $template = $theme['errors']['template']['404'];
                }
                $template = str_replace('{side}', 'backend', $template);
                $listener->setNotFoundTemplate($template);
                // layout
                if (isset($theme['errors']['layout'])) {
                    $layout = $theme['errors']['layout'];
                }
                $layout = str_replace('{side}', 'backend', $layout);
                $event->getTarget()->layout($layout);
                break;
            case $target instanceof AbstractInstallation:
                $theme = $options->getBackendTheme();
                // template
                if (isset($theme['errors']['template']['404'])) {
                    $template = $theme['errors']['template']['404'];
                }
                $template = str_replace('{side}', 'installation', $template);
                $listener->setNotFoundTemplate($template);
                // layout
                if (isset($theme['errors']['layout'])) {
                    $layout = $theme['errors']['layout'];
                }
                $layout = str_replace('{side}', 'installation', $layout);
                $event->getTarget()->layout($layout);
                break;
            case $target instanceof AbstractFront:
                $theme = $options->getFrontendTheme();
                // template
                if (isset($theme['errors']['template']['404'])) {
                    $template = $theme['errors']['template']['404'];
                }
                $template = str_replace('{side}', 'frontend', $template);
                $listener->setNotFoundTemplate($template);
                // layout
                if (isset($theme['errors']['layout'])) {
                    $layout = $theme['errors']['layout'];
                }
                $layout = str_replace('{side}', 'frontend', $layout);
                $event->getTarget()->layout($layout);
                break;
        }
    }
}
