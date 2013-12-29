<?php

namespace ScContent\Listener\Theme;

use Zend\Mvc\MvcEvent;

class CommonStrategy extends AbstractThemeStrategy
{
    public function update(MvcEvent $event)
    {
        if ($event->getResult()->terminate()) {
            return;
        }

        $app = $event->getApplication();
        $sm = $app->getServiceManager();

        $viewManager = $sm->get('ViewManager');
        $renderer = $viewManager->getRenderer();

        $moduleOptions = $this->getModuleOptions();
        $theme = $moduleOptions->getFrontendThemeName();
        $options = $moduleOptions->getFrontendTheme();
        $options = $options['frontend'];

        $layout = $theme . '/layout/frontend/index';
        if (isset($options['layout'])) {
            $layout = $options['layout'];
        }
        $renderer->layout()->setTemplate($layout);
    }
}
