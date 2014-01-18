<?php

namespace ScContent\Controller\Back;

use ScContent\Controller\AbstractBack,
    ScContent\Service\Back\WidgetConfigurationService,
    ScContent\Form\Back\WidgetConfigurationForm,
    ScContent\Exception\RuntimeException,
    //
    Zend\View\Model\ViewModel;

class WidgetController extends AbstractBack
{
    /**
     * @var ScContent\Service\Back\WidgetConfigurationService
     */
    protected $widgetConfigurationService;

    /**
     * @var ScContent\Form\Back\WidgetConfigurationForm
     */
    protected $widgetConfigurationForm;

    public function configureAction()
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
        $service = $this->getWidgetConfigurationService();
        try {
            $widget = $service->findWidget($id);
            $view->theme = $widget->getTheme();
        } catch (RuntimeException $e) {
            $this->flashMessenger()->addMessage($e->getMessage());
            return $this->redirect()
                ->toRoute('sc-admin/themes')
                ->setStatusCode(303);
        }
        if ($widget->findOption('immutable')) {
            $this->flashMessenger()->addMessage(sprintf(
                $this->scTranslate(
                    "The widget with identifier '%s' is immutable."
                ),
                $id
            ));
            return $this->redirect()
                ->toRoute('sc-admin/layout', ['theme' => $widget->getTheme()])
                ->setStatusCode(303);
        }

        $form = $this->getWidgetConfigurationForm();
        $form->bind($widget);
        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());
            if ($form->isValid()) {
                try {
                    $service->saveWidget($widget);
                } catch (RuntimeException $e) {
                    $view->messages = [$e->getMessage()];
                }
            }
        }
        $view->form = $form;
        return $view;
    }

    /**
     * @param ScContent\Service\Back\WidgetConfigurationService $service
     * @return void
     */
    public function setWidgetConfigurationService(WidgetConfigurationService $service)
    {
        $this->widgetConfigurationService = $service;
    }

    /**
     * @return ScContent\Service\Back\WidgetConfigurationService
     */
    public function getWidgetConfigurationService()
    {
        if (! $this->widgetConfigurationService instanceof WidgetConfigurationService) {
            $serviceLocator = $this->getServiceLocator();
            $this->widgetConfigurationService = $serviceLocator->get(
                'ScService.Back.WidgetConfiguration'
            );
        }
        return $this->widgetConfigurationService;
    }

    /**
     * @param ScContent\Form\Back\WidgetConfigurationForm $form
     * @return void
     */
    public function setWidgetConfigurationForm(WidgetConfigurationForm $form)
    {
        $this->widgetConfigurationForm = $form;
    }

    /**
     * @return ScContent\Form\Back\WidgetConfigurationForm
     */
    public function getWidgetConfigurationForm()
    {
        if (! $this->widgetConfigurationForm instanceof WidgetConfigurationForm) {
            $formElementManager = $this->getServiceLocator()->get(
                'FormElementManager'
            );
            $this->widgetConfigurationForm = $formElementManager->get(
                'ScForm.Back.WidgetConfiguration'
            );
        }
        return $this->widgetConfigurationForm;
    }
}
