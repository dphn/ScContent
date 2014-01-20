<?php

namespace ScContent\Listener\Theme;

use ScContent\Mapper\Theme\FrontendLayoutMapper,
    ScContent\Service\Front\ContentService,
    ScContent\Controller\AbstractWidget,
    ScContent\Controller\AbstractFront,
    ScContent\Exception\IoCException,
    //
    Zend\Mvc\Controller\ControllerManager,
    Zend\View\Model\ModelInterface as ViewModel,
    Zend\Mvc\MvcEvent;

class FrontendStrategy extends AbstractThemeStrategy
{
    /**
     * @var Zend\Mvc\Controller\ControllerManager
     */
    protected $controllerManager;

    /**
     * @var ScContent\Service\Front\ContentService
     */
    protected $contentService;

    /**
     * @var ScContent\Mapper\Theme\FrontendLayoutMapper
     */
    protected $layoutMapper;

    /**
     * @param Zend\Mvc\Controller\ControllerManager $manager
     * @return FrontendStrategy
     */
    public function setControllerManager(ControllerManager $manager)
    {
        $this->controllerManager = $manager;
    }

    /**
     * @throws ScContent\Exception\IoCException
     * @return Zend\Mvc\Controller\ControllerManager
     */
    public function getControllerManager()
    {
        if (! $this->controllerManager instanceof ControllerManager) {
            throw new IoCException(
                'The controller manager was not set.'
            );
        }
        return $this->controllerManager;
    }

    /**
     * @param ScContent\Service\Front\ContentService
     * @return FrontendStrategy
     */
    public function setContentService(ContentService $service)
    {
        $this->contentService = $service;
        return $this;
    }

    /**
     * @throws ScContent\Exception\IoCException
     * @return ScContent\Service\Front\ContentService
     */
    public function getContentService()
    {
        if (! $this->contentService instanceof ContentService) {
            throw new IoCException(
                'The content service was not set.'
            );
        }
        return $this->contentService;
    }

    /**
     * @param ScContent\Mapper\Theme\FrontendLayoutMapper $mapper
     * @return FrontendStrategy
     */
    public function setLayoutMapper(FrontendLayoutMapper $mapper)
    {
        $this->layoutMapper = $mapper;
        return $this;
    }

    /**
     * @throws ScContent\Exception\IoCException
     * @return ScContent\Mapper\Theme\FrontendLayoutMapper
     */
    public function getLayotMapper()
    {
        if (! $this->layoutMapper instanceof FrontendLayoutMapper) {
            throw new IoCException(
	       'The layout mapper was not set.'
            );
        }
        return $this->layoutMapper;
    }

    /**
     * @param Zend\Mvc\MvcEvent
     * @return FrontendStrategy
     */
    public function update(MvcEvent $event)
    {
        $moduleOptions = $this->getModuleOptions();

        $theme = $moduleOptions->getFrontendThemeName();
        $options = $moduleOptions->getFrontendTheme();
        $options = $options['frontend'];

        $controller = $event->getTarget();
        $model = $event->getResult();

        if (! $model instanceof ViewModel) {
            return $this;
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
            return $this;
        }

        $layout = $controller->layout();

        $template = $theme . '/layout/frontend/index';
        if(isset($options['layout'])) {
            $template = $options['layout'];
        }
        $layout->setTemplate($template);

        if ($event->getParam(AbstractFront::EnableRegions, true)) {
            $this->buildRegions($controller, $layout);
        }
        return $this;
    }

    protected function buildRegions(AbstractFront $controller, ViewModel $layout)
    {
        $mapper = $this->getLayotMapper();
        $moduleOptions = $this->getModuleOptions();
        $controllerManager = $this->getControllerManager();
        $contentService = $this->getContentService();
        $content = $contentService->getContent();

        $regions = $mapper->findRegions($content->getId());
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
        return $this;
    }
}
