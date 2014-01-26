<?php

namespace ScContent\Controller\Back;

use ScContent\Controller\AbstractBack,
    ScContent\Options\ModuleOptions,
    //
    Zend\View\Model\ViewModel;

class WidgetVisibilityController extends AbstractWidgetAwareController
{
    /**
     * @var ScContent\Options\ModuleOptions
     */
    protected $moduleOptions;

    public function indexAction()
    {
        $id = $this->params()->fromRoute('id');
        if (! is_numeric($id)) {
            $this->flashMessenger()->addMessage(
                $this->scTranslate('The widget identifier was not specified.')
            );
            return $this->redirect()
                ->toRoute('sc-admin/themes')
                ->setStatusCode(303);
        }

        $view = new ViewModel;
        $widget = $this->deriveWidget($id);
        if (empty($widget)) {
            return $this->getResponse();
        }
        $view->widgetId = $widget->getId();
        $view->theme = $widget->getTheme();
        $moduleOptions = $this->getModuleOptions();
        $view->config = $moduleOptions->getWidgetByName($widget->getName());
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
}
