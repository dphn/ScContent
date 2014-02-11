<?php

namespace ScContent\Listener\Theme;

use ScContent\Listener\Installation\InstallationInspector,
    ScContent\Controller\AbstractInstallation,
    ScContent\Exception\InvalidArgumentException,
    ScContent\Exception\IoCException,
    //
    Zend\View\Model\ModelInterface as ViewModel,
    Zend\Mvc\MvcEvent;

class InstallationStrategy extends AbstractThemeStrategy
{
    protected $installationInspector;

    public function setInstallationInspector(InstallationInspector $service)
    {
        $this->installationInspector = $service;
    }

    public function getInstallationInspector()
    {
        if (! $this->installationInspector instanceof InstallationInspector) {
            throw new IoCException(
	       'The InstallationInspector was not set.'
            );
        }
        return $this->installationInspector;
    }

    public function update(MvcEvent $event)
    {
        $installationInspector = $this->getInstallationInspector();
        $options = $installationInspector->getCurrentSetup();

        $routeMatch = $event->getRouteMatch();
        $controller = $event->getTarget();
        $model = $event->getResult();

        if (! $model instanceof ViewModel) {
            return;
        }

        if (! $controller instanceof AbstractInstallation) {
            throw new InvalidArgumentException(sprintf(
                "The operation is not applicable to the type of target '%s'.",
                get_class($controller)
            ));
        }
        $layout = 'sc-default/layout/installation/index';
        $template = 'sc-default/template/installation/index';

        if (isset($options['layout'])) {
            $layout = $options['layout'];
        }
        if (isset($options['template'])) {
            $template = $options['template'];
        }
        $step = $options['steps'][$routeMatch->getParam('step')];
        if (isset($step['layout'])) {
            $layout = $step['layout'];
        }
        if (isset($step['template'])) {
            $template = $step['template'];
        }

        $layoutModel = $event->getViewModel();
        if (! $model->terminate()) {
            if ('layout/layout' == $layoutModel->getTemplate()) {
                $layoutModel->setTemplate($layout);
            }
            if (isset($options['title']) && ! isset($layoutModel->title)) {
                $layoutModel->title = $options['title'];
            }
        }

        if (! $model->getTemplate()) {
            $model->setTemplate($template);
        }

        if (isset($options['brand']) && ! isset($model->brand)) {
            $model->brand = $options['brand'];
        }
        if (isset($options['header']) && ! isset($model->header)) {
            $model->header = $options['header'];
        }

        if (! isset($model->step)) {
            $model->step = $routeMatch->getParam('step');
        }
        if (isset($step['title']) && ! isset($model->title)) {
            $model->title = $step['title'];
        }
        if (isset($step['header']) && ! isset($model->header)) {
            $model->header = $step['header'];
        }
        if (isset($step['info']) && ! isset($model->info)) {
            $model->info = $step['info'];
        }
    }
}
