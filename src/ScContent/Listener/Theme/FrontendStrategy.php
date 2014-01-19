<?php

namespace ScContent\Listener\Theme;

use ScContent\Mapper\Theme\FrontendLayoutMapper,
    ScContent\Controller\AbstractWidget,
    ScContent\Controller\AbstractFront,
    ScContent\Exception\IoCException,
    //
    Zend\Mvc\Controller\ControllerManager,
    Zend\View\Model\ModelInterface as ViewModel,
    Zend\Mvc\MvcEvent;

class FrontendStrategy extends AbstractThemeStrategy
{
    protected $controllerManager;

    protected $layoutMapper;

    public function setControllerManager(ControllerManager $manager)
    {
        $this->controllerManager = $manager;
    }

    public function getControllerManager()
    {
        if (! $this->controllerManager instanceof ControllerManager) {
            throw new IoCException(
                'The controller manager was not set.'
            );
        }
        return $this->controllerManager;
    }

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
        $controllerManager = $this->getControllerManager();
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

        if (! $event->getParam(AbstractFront::EnableRegions, true)) {
            return;
        }

        $regions = $mapper->findRegions();
        $layout->regions = $regions;

        foreach ($regions as $widgetsList) {
            foreach ($widgetsList as $item) {
                $widgetName = $item->getName();

                if (! $moduleOptions->widgetExists($widgetName)) {
                    continue;
                }
                $widget = $moduleOptions->getWidgetByName($widgetName);

                if ($widgetName === 'content') {
                    $item->setId($item->getName());
                    continue;
                }

                if (! isset($widget['frontend'])) {
                    continue;
                }

                if (! $controllerManager->has($widget['frontend'])) {
                    continue;
                }

                $widgetController = $controllerManager->get($widget['frontend']);
                if (! $widgetController instanceof AbstractWidget) {
                    continue;
                }
                $widgetController->setItem($item);

                $childModel = $controller->forward()->dispatch(
                    $widget['frontend'],
                    ['action' => 'front']
                );
                $layout->addChild($childModel, $item->getId());
            }
        }
    }
}
