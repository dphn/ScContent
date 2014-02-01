<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013-2014 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Controller\Back;

use ScContent\Controller\AbstractBack,
    ScContent\Options\ModuleOptions,
    ScContent\Service\Back\WidgetVisibilityService,
    //
    Zend\View\Model\ViewModel,
    Zend\Http\Response;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class WidgetVisibilityController extends AbstractWidgetAwareController
{
    /**
     * @var ScContent\Options\ModuleOptions
     */
    protected $moduleOptions;

    /**
     * @var ScContent\Service\Back\WidgetVisibilityService
     */
    protected $visibilityService;

    /**
     * Show content list with widget visibility options.
     *
     * @return Zend\Stdlib\ResponseInterface | Zend\View\Model\ViewModel
     */
    public function indexAction()
    {
        $visibilityService = $this->getVisibilityService();
        $options = $visibilityService->getOptions();

        $widgetId = $options->getWidgetId();
        if (! $widgetId) {
            $this->flashMessenger()->addMessage(
                $this->scTranslate('The widget identifier was not specified.')
            );
            return $this->redirect()
                ->toRoute('sc-admin/themes')
                ->setStatusCode(303);
        }

        $widget = $this->deriveWidget($widgetId);
        if (empty($widget)) {
            return $this->getResponse();
        }
        if ($this->getRequest()->isPost()) {
            $event = $this->request->getPost('suboperation');
            if (! empty($event)) {
                $events = $this->getEventManager();
                $params = $this->getRequest()->getPost();
                $params['widget_id'] = $widget->getId();
                $result = $events->trigger($event, $this, $params);
                if ($result->last() instanceof Response) {
                    return $result->last();
                }
            }
        }

        $view = new ViewModel;
        $view->options = $options;
        $view->widget = $widget;

        $moduleOptions = $this->getModuleOptions();
        $view->config = $moduleOptions->getWidgetByName($widget->getName());

        $view->list = $visibilityService->getContentList();
        $visibilityService->saveOptions();

        return $view;
    }

    /**
     * @param ScContent\Options\ModuleOptions $options
     * @return void
     */
    public function setModuleOptions(ModuleOptions $options)
    {
        $this->moduleOptions = $options;
    }

    /**
     * @return ScContent\Options\ModuleOptions
     */
    public function getModuleOptions()
    {
        if (! $this->moduleOptions instanceof ModuleOptions) {
            $serviceLocator = $this->getServiceLocator();
            $this->moduleOptions = $serviceLocator->get(
                'ScOptions.ModuleOptions'
            );
        }
        return $this->moduleOptions;
    }

    /**
     * @param ScContent\Service\Back\WidgetVisibilityService
     * @return void
     */
    public function setVisibilityService(WidgetVisibilityService $service)
    {
        $this->visibilityService = $service;
    }

    /**
     * @return ScContent\Service\Back\WidgetVisibilityService
     */
    public function getVisibilityService()
    {
        if (! $this->visibilityService instanceof WidgetVisibilityService) {
            $this->visibilityService = $this->getServiceLocator()->get(
                'ScService.Back.WidgetVisibility'
            );
        }
        return $this->visibilityService;
    }
}
