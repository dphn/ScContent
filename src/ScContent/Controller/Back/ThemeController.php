<?php

namespace ScContent\Controller\Back;

use ScContent\Controller\AbstractBack,
    ScContent\Service\Installation\LayoutService,
    ScContent\Options\ModuleOptions,
    //
    Zend\View\Model\ViewModel;

class ThemeController extends AbstractBack
{
    /**
     * @var ScContent\Options\ModuleOptions
     */
    protected $moduleOptions;
    
    /**
     * @var ScContent\Service\Installation\LayoutService
     */
    protected $layoutService;
    
    public function indexAction()
    {
        $view = new ViewModel();
        $layout = $this->getLayoutService();
        $view->registeredThemes = $layout->getRegisteredThemes();
        $view->options = $this->getModuleOptions();
        $flashMessenger = $this->flashMessenger();
        if($flashMessenger->hasMessages()) {
            $view->messages = $flashMessenger->getMessages();
        }
        return $view;
    }
    
    /**
     * @param ScContent\Options\ModuleOptions $options
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
        if(!$this->moduleOptions instanceof ModuleOptions) {
            $serviceLocator = $this->getServiceLocator();
            $this->moduleOptions = $serviceLocator->get('sc-options.module');
        }
        return $this->moduleOptions;
    }
    
    public function setLayoutService(LayoutService $service)
    {
        $this->layoutService = $service;
    }
    
    public function getLayoutService()
    {
        if(!$this->layoutService instanceof LayoutService) {
            $serviceLocator = $this->getServiceLocator();
            $this->layoutService = $serviceLocator->get('sc-service.installation.layout');
        }
        return $this->layoutService;
    }
}
