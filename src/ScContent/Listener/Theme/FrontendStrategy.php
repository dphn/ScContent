<?php

namespace ScContent\Listener\Theme;

use ScContent\Mapper\Theme\FrontendLayoutMapper,
    ScContent\Exception\IoCException,
    //
    Zend\View\Model\ModelInterface as ViewModel,
    Zend\Mvc\MvcEvent;

class FrontendStrategy extends AbstractThemeStrategy
{
    protected $layoutMapper;

    public function setLayoutMapper(FrontendLayoutMapper $mapper)
    {
        $this->layoutMapper = $mapper;
    }

    public function getLayotMapper()
    {
        if (! $this->layoutMapper instanceof FrontendLayoutMapper) {
            throw new IoCException(
	       'The layout mapper was not set.'
            );
        }
        return $this->layoutMapper;
    }

    public function update(MvcEvent $event)
    {
        $moduleOptions = $this->getModuleOptions();
        $mapper = $this->getLayotMapper();

        $theme = $moduleOptions->getFrontendThemeName();
        $options = $moduleOptions->getFrontendTheme();
        $options = $options['frontend'];

        $controller = $event->getTarget();
        $model = $event->getResult();
        $sm = $controller->getServiceLocator();

        if (! $model instanceof ViewModel) {
            return;
        }

        $template = $model->getTemplate();
        if (empty($template)) {
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
        }

        if ($event->getResult()->terminate()) {
            return;
        }

        $layout = $controller->layout();

        $template = $theme . '/layout/frontend/index';
        if(isset($options['layout'])) {
            $template = $options['layout'];
        }
        $layout->setTemplate($template);

        $regions = $mapper->findRegions();
        $layout->regions = $regions;

        $widgets = $moduleOptions->getWidgets();

        foreach($widgets as $widgetName => $widgetOptions) {
            if (! isset($widgetOptions['frontend'])) {
                continue;
            }
            $widget = $controller->forward()->dispatch(
                $widgetOptions['frontend'],
                ['action' => 'front']
            );

            $layout->addChild($widget, $widgetName);
        }
    }
}
