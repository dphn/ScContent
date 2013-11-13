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
        $options = $moduleOptions->getBackendTheme();
        $options = $options['frontend'];
        
        $routeMatch = $event->getRouteMatch();
        $controller = $event->getTarget();
        $model = $event->getResult();
        $sm = $controller->getServiceLocator();
        
        if(!$model instanceof ViewModel) {
            return;
        }
        
        $class = get_class($controller);
        $class = $this->deriveControllerClass($class);
        
        $template = $theme . '/template/frontend/';
        $template .= $this->inflectName($class);
        
        $action  = $routeMatch->getParam('action');
        if (null !== $action) {
            $template .= '/' . $this->inflectName($action);
        }
        $model->setTemplate($template);
        
        if($event->getResult()->terminate()) {
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
            if(isset($widgetOptions['default_region'])) {
                $defaultRegion = $widgetOptions['default_region'];
                if(array_key_exists($defaultRegion, $registry)) {
                    $registry[$defaultRegion][] = $widgetName;
                }
            }
            $invokables = $widgetOptions['invokables'];
            $widget = $controller->forward()->dispatch(
                $invokables['controller'],
                array('action' => $invokables['action'])
            );
            
            $item = $sm->get($invokables['controller']);
            $class = get_class($item);
            $template = $theme . '/template/backend/';
            $template .= $this->inflectName($controller);
            
            $action  = $invokables['action'];
            $template .= '/' . $this->inflectName($action);
            
            $widget->setTemplate($template);
            $widget->theme = $theme;
            
            $layout->addChild($widget, $widgetName);
        }
        $layout->registry = $registry;
    }
}
