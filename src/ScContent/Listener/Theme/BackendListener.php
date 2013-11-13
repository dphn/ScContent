<?php

namespace ScContent\Listener\Theme;

use Zend\View\Model\ModelInterface as ViewModel,
    Zend\Mvc\MvcEvent;

class BackendListener extends AbstractThemeListener
{
    public function update(MvcEvent $event)
    {
        $moduleOptions = $this->getModuleOptions();
        $theme = $moduleOptions->getBackendThemeName();
        
        $routeMatch = $event->getRouteMatch();
        $controller = $event->getTarget();
        $model = $event->getResult();
        
        if(!$model instanceof ViewModel) {
            return;
        }
        
        if(!$event->getResult()->terminate()) {
            $layout = $theme . '/layout/backend/index';
            $controller->layout($layout);
        }
        
        if(is_object($controller)) {
            $controller = get_class($controller);
        }
        if(!$controller) {
            $controller = $routeMatch->getParam('controller', '');
        }
        
        $controller = $this->deriveControllerClass($controller);
        
        $template = $theme . '/template/backend/';
        $template .= $this->inflectName($controller);
        
        $action  = $routeMatch->getParam('action');
        if (null !== $action) {
            $template .= '/' . $this->inflectName($action);
        }
        $model->setTemplate($template);
    }
}
