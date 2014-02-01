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

use ScContent\Form\Back\WidgetConfigurationForm,
    ScContent\Options\ModuleOptions,
    ScContent\Exception\RuntimeException,
    //
    Zend\Mvc\Controller\ControllerManager,
    Zend\View\Model\ViewModel;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class WidgetController extends AbstractWidgetAwareController
{
    /**
     * @var Zend\Mvc\Controller\ControllerManager
     */
    protected static $controllerLoader;

    /**
     * @var ScContent\Form\Back\WidgetConfigurationForm
     */
    protected $widgetConfigurationForm;

    /**
     * @var ScContent\Options\ModuleOptions
     */
    protected $moduleOptions;

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
        $widget = $this->deriveWidget($id);
        if (empty($widget)) {
            return $this->getResponse();
        }

        $service = $this->getWidgetConfigurationService();
        $widgetConfig = $service->getWidgetConfig($widget);

        $view->widget = $widget;
        $moduleOptions = $this->getModuleOptions();
        $view->config = $moduleOptions->getWidgetByName($widget->getName());

        $form = $this->getWidgetConfigurationForm();
        $form->setAttribute(
            'action',
            $this->url()->fromRoute('sc-admin/widget/configure', ['id' => $id])
        );
        $form->bind($widgetConfig);
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

    public function editAction()
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
        $widget = $this->deriveWidget($id);
        if (empty($widget)) {
            return $this->getResponse();
        }

        $moduleOptions = $this->getModuleOptions();
        $config = $moduleOptions->getWidgetByName($widget->getName());

        if (! isset($config['backend'])) {
            $this->flashMessenger()->addMessage(sprintf(
                $this->scTranslate(
                    "The widget '%s' is not editable."
                ),
                $widget->getDisplayName()
            ));
            return $this->redirect()
                ->toRoute('sc-admin/layout', ['theme' => $widget->getTheme()])
                ->setStatusCode(303);
        }

        $controllerLoader = $this->getControllerLoader();
        if (! $controllerLoader->has($config['backend'])) {
            $this->flashMessenger()->addMessage(sprintf(
                $this->scTranslate(
                    "Unable to find component to edit the widget '%s'."
                ),
                $widget->getDisplayName()
            ));
            return $this->redirect()
                ->toRoute('sc-admin/layout', ['theme' => $widget->getTheme()])
                ->setStatusCode(303);
        }

        $controller = $controllerLoader->get($config['backend']);
        $controller->setItem($widget);

        $view = new ViewModel([
            'widget' => $widget,
        ]);

        $childView = $this->forward()->dispatch(
            $config['backend'],
            ['action' => 'back']
        );

        if ($widget) {
            $service = $this->getWidgetConfigurationService();
            $service->saveWidget($widget);
        }

        $view->addChild($childView, 'backend_of_custom_widget');
        return $view;
    }

    /**
     * @param Zend\Mvc\Controller\ControllerManager $loader
     * @return void
     */
    public function setControllerLoader(ControllerManager $loader)
    {
        self::$controllerLoader = $loader;
    }

    /**
     * @return Zend\Mvc\Controller\ControllerManager
     */
    public function getControllerLoader()
    {
        if (! self::$controllerLoader instanceof ControllerManager) {
            $serviceLocator = $this->getServiceLocator();
            self::$controllerLoader = $serviceLocator->get(
                'ControllerLoader'
            );
        }
        return self::$controllerLoader;
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
