<?php

namespace ScContent\Controller\Plugin;

use ScContent\Validator\Options\WidgetOptionsValidator,
    ScContent\Validator\Controller\WidgetValidator,
    //
    Zend\Mvc\Controller\Plugin\AbstractPlugin,
    Zend\Mvc\Controller\ControllerManager;

class WidgetAvailability extends AbstractPlugin
{
    /**
     * @var Zend\Mvc\Controller\ControllerManager
     */
    protected $loader;
    
    /**
     * @var ScContent\Validator\Controller\WidgetValidator
     */
    protected $widgetValidator;
    
    /**
     * @var ScContent\Validator\Options\WidgetOptionsValidator
     */
    protected $widgetOptionsWalidator;
    
    /**
     * @var string
     */
    protected $section = '';
    
    /**
     * @param array $widget
     * @param string $section
     * @return boolean
     */
    public function __invoke($widget, $section)
    {
        if(!$this->getWidgetOptionsValidator($section)->isValid($widget)) {
            return false;
        }
        $source = $widget['invokables'][$section];
        if(!$this->getWidgetValidator()->isValid(array(
            'controller' => $source['controller'],
            'action'     => $source['action']
        ))) {
            return false;
        }
        return true;
    }
    
    /**
     * @return string
     */
    public function getSection()
    {
        return $this->section();
    }
    
    /**
     * @param string $section
     */
    public function setSection($section)
    {
        $this->section = $section;
    }
    
    /**
     * @return Zend\Mvc\Controller\ControllerManager
     */
    public function getLoader()
    {
        if(is_null($this->loader)) {
            $this->loader
                = $this->getController()
                       ->getServiceLocator()
                       ->get('ControllerLoader');
        }
        return $this->loader;
    }
    
    /**
     * @param Zend\Mvc\Controller\ControllerManager $loader
     */
    public function setLoader(ControllerManager $loader)
    {
        $this->loader = $loader;
    }
    
    /**
     * @param ScContent\Validator\Controller\WidgetValidator $validator
     */
    public function setWidgetValidator(WidgetValidator $validator)
    {
        $this->widgetValidator = $validator;
    }
    
    /**
     * @return ScContent\Validator\Controller\WidgetValidator
     */
    public function getWidgetValidator()
    {
        if(is_null($this->widgetValidator)) {
            $this->widgetValidator = new WidgetValidator($this->getLoader());
        }
        return $this->widgetValidator;
    }
    
    /**
     * @param string $section
     * @return ScContent\Validator\Options\WidgetOptionsValidator
     */
    public function getWidgetOptionsValidator($section)
    {
        if($section !== $this->section
            || is_null($this->widgetOptionsWalidator)
        ) {
            $this->widgetOptionsValidator
                = new WidgetOptionsValidator($section);
            $this->setSection($section);
        }
        return $this->widgetOptionsValidator;
    }
    
    /**
     * @param ScContent\Validator\Options\WidgetOptionsValidator $validator
     */
    public function setWidgetOptionsValidator(WidgetOptionsValidator $validator)
    {
        $this->widgetOptionsWalidator = $validator;
    }
}
