<?php

namespace ScContent\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Application\ConfigAwareInterface;

use Zend\Debug\Debug;

class FrontController extends AbstractActionController
{
    protected $config;
    
    public function indexAction()
    {
        $page = new ViewModel();
        $widgets = isset($this->config['widgets'])
                 ? $this->config['widgets']
                 : null;
        $regions = $this->config['regions'];
        $registry = array();
        foreach($widgets as $widgetName => $widget) {
            $frontend = $widget['invokables']['frontend'];
            $widget = $this->forward()->dispatch(
                $frontend['controller'],
                array('action' => $frontend['action'])
            );
            if(isset($regions[$widgetName])) {
                $page->addChild($widget,$widgetName);
                $registry[$regions[$widgetName]][] = $widgetName;
            }
        }
        $page->registry = $registry;
        return $page;
    }
    
    public function contentAction()
    {
        $widget = new ViewModel();
        return $widget;
    }
    
    public function setConfig($config)
    {
        $this->config = $config;
    }
}