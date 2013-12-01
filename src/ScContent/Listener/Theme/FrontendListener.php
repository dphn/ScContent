<?php

namespace ScContent\Listener\Theme;

use Zend\View\Model\ModelInterface as ViewModel,
    Zend\Mvc\MvcEvent;

class FrontendListener extends AbstractThemeListener
{
    public function update(MvcEvent $event)
    {
        $moduleOptions = $this->getModuleOptions();
        $theme = $moduleOptions->getFrontendThemeName();
        $options = $moduleOptions->getFrontendTheme();
        $options = $options['frontend'];

        $controller = $event->getTarget();
        $model = $event->getResult();
        $sm = $controller->getServiceLocator();

        if (! $model instanceof ViewModel) {
            return;
        }

        $class = get_class($controller);
        $class = $this->deriveControllerClass($class);

        $template = $theme . '/template/frontend/';
        $template .= $this->inflectName($class);

        $routeMatch = $event->getRouteMatch();
        $action  = $routeMatch->getParam('action');
        if (null !== $action) {
            $template .= '/' . $this->inflectName($action);
        }
        $model->setTemplate($template);

        if ($event->getResult()->terminate()) {
            return;
        }

        $layout = $controller->layout();

        $template = $theme . '/layout/frontend/index';
        if(isset($options['layout'])) {
            $template = $options['layout'];
        }
        $layout->setTemplate($template);

        $registry = array_fill_keys(array_keys($options['regions']), array());

        $widgets = $moduleOptions->getWidgets();
        foreach($widgets as $widgetName => $widgetOptions) {
            /*
            if(isset($widgetOptions['default_region'])) {
                $defaultRegion = $widgetOptions['default_region'];
                if(array_key_exists($defaultRegion, $registry)) {
                    $registry[$defaultRegion][] = $widgetName;
                }
            }
            */

            if (! isset($widgetOptions['frontend'])) {
                continue;
            }
            $invokables = $widgetOptions['frontend'];

            $widget = $controller->forward()->dispatch(
                $invokables['controller'],
                array('action' => $invokables['action'])
            );

            $layout->addChild($widget, $widgetName);
        }
        $layout->registry = $registry;
    }
}
