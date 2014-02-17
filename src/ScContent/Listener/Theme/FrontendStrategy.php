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

use ScContent\Mapper\Theme\FrontendLayoutMapper,
    ScContent\Service\Front\ContentService,
    ScContent\Controller\AbstractWidget,
    ScContent\Controller\AbstractFront,
    ScContent\Exception\DomainException,
    ScContent\Exception\IoCException,
    //
    Zend\View\Model\ModelInterface as ViewModel,
    Zend\Mvc\Controller\ControllerManager,
    Zend\Mvc\MvcEvent;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class FrontendStrategy extends AbstractThemeStrategy
{
    /**
     * @var string
     */
    protected static $side = 'frontend';

    /**
     * @var \Zend\Mvc\Controller\ControllerManager
     */
    protected $controllerManager;

    /**
     * @var \ScContent\Service\Front\ContentService
     */
    protected $contentService;

    /**
     * @var \ScContent\Mapper\Theme\FrontendLayoutMapper
     */
    protected $layoutMapper;

    /**
     * @param  \Zend\Mvc\Controller\ControllerManager $manager
     * @return FrontendLayoutService
     */
    public function setControllerManager(ControllerManager $manager)
    {
        $this->controllerManager = $manager;
    }

    /**
     * @throws \ScContent\Exception\IoCException
     * @return \Zend\Mvc\Controller\ControllerManager
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
     * @param  \ScContent\Service\Front\ContentService
     * @return FrontendLayoutService
     */
    public function setContentService(ContentService $service)
    {
        $this->contentService = $service;
        return $this;
    }

    /**
     * @throws \ScContent\Exception\IoCException
     * @return \ScContent\Service\Front\ContentService
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
     * @param  \ScContent\Mapper\Theme\FrontendLayoutMapper $mapper
     * @return FrontendLayoutService
     */
    public function setLayoutMapper(FrontendLayoutMapper $mapper)
    {
        $this->layoutMapper = $mapper;
        return $this;
    }

    /**
     * @throws \ScContent\Exception\IoCException
     * @return \ScContent\Mapper\Theme\FrontendLayoutMapper
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
     * @param  \Zend\Mvc\MvcEvent $event
     * @throws \ScContent\Exception\DomainException
     * @return void
     */
    public function update(MvcEvent $event)
    {
        $target = $event->getTarget();
        if (! $target instanceof AbstractFront) {
            throw new DomainException(sprintf(
                "Frontend theme strategy is not applicable to current target '%s'.",
                is_object($target) ? get_class($target) : gettype($target)
            ));
        }
        $this->injectContentTemplate($event)
            ->injectLayoutTemplate($event)
            ->buildLayout($event);
    }

    /**
     * @param  \Zend\Mvc\MvcEvent $event
     * @return FrontendStrategy
     */
    protected function buildLayout(MvcEvent $event)
    {
        if (! $event->getParam(AbstractFront::EnableRegions, true)) {
            return $this;
        }

        $result = $event->getResult();
        if (! $result instanceof ViewModel || $result->terminate()) {
            return $this;
        }

        $controller = $event->getTarget();
        if (! $controller instanceof AbstractFront) {
            return $this;
        }

        $layout = $event->getViewModel();
        if (! $layout instanceof ViewModel) {
            return $this;
        }

        $moduleOptions = $this->getModuleOptions();
        $controllerManager = $this->getControllerManager();

        $regions = $this->getRegions();
        $layout->regions = $regions;

        foreach ($regions as $widgetsList) {
            foreach ($widgetsList as $item) {
                $widgetName = $item->getName();
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

    /**
     * @return \ScContent\Entity\Front\Regions
     */
    protected function getRegions()
    {
        $mapper = $this->getLayotMapper();
        $moduleOptions = $this->getModuleOptions();

        $contentService = $this->getContentService();
        $content = $contentService->getContent();

        $themeName = $moduleOptions->getFrontendThemeName();
        $regions = $mapper->findRegions($themeName, $content->getId());

        return $regions;
    }
}
