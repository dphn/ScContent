<?php

namespace ScContent\Listener\Theme;

use ScContent\Service\Installation\InstallationInspector,
    ScContent\Controller\AbstractInstallation,
    ScContent\Exception\InvalidArgumentException,
    ScContent\Exception\IoCException,
    //
    Zend\View\Model\ModelInterface as ViewModel,
    Zend\Mvc\MvcEvent;

class InstallationListener extends AbstractThemeListener
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

        if(!$model instanceof ViewModel) {
            return;
        }

        if(!$controller instanceof AbstractInstallation) {
            throw new InvalidArgumentException(sprintf(
                "The operation is not applicable to the type of target '%s'.",
                get_class($controller)
            ));
        }
        $layout = 'sc-default/layout/installation/index';
        $template = 'sc-default/template/installation/index';

        if(isset($options['layout'])) {
            $layout = $options['layout'];
        }
        if(isset($options['template'])) {
            $template = $options['template'];
        }
        $step = $options['steps'][$routeMatch->getParam('step')];
        if(isset($step['layout'])) {
            $layout = $step['layout'];
        }
        if(isset($step['template'])) {
            $template = $step['template'];
        }

        if(!$model->terminate()) {
            $event->getViewModel()->setTemplate($layout);
            if(isset($options['title'])) {
                $event->getViewModel()->title = $options['title'];
            }
        }

        $model->setTemplate($template);
        if(isset($options['header'])) {
            $model->header = $options['header'];
        }
        $model->step = $routeMatch->getParam('step');
        if(isset($step['title'])) {
            $model->title = $step['title'];
        }
        if(isset($step['info'])) {
            $model->info = $step['info'];
        }
    }
}
