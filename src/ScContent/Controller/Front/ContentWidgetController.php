<?php

namespace ScContent\Controller\Front;

use ScContent\Controller\AbstractWidget,
    ScContent\Options\ModuleOptions,
    //
    Zend\View\Model\ViewModel;

class ContentWidgetController extends AbstractWidget
{
    const Template = '/templates/frontend/content';
    protected $moduleOptions;

    public function frontAction()
    {
        $view = new ViewModel();

        $options = $this->getModuleOptions();
        $theme = $options->getFrontendThemeName();
        $template =
    }

    public function backAction()
    {

    }

    public function setModuleOptions(ModuleOptions $options)
    {
        $this->moduleOptions = $options;
    }

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
