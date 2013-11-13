<?php

namespace ScContent\Validator\Options;

use Zend\Validator\AbstractValidator;

class WidgetOptionsValidator extends AbstractValidator
{
    /**#@+
     * @const string
     */
    const Controller   = 'controller';
    const Action       = 'action';
    const ConfigurationError = 'Configuration Error';
    //
    const FrontendSetion = 'frontend';
    const BackendSection = 'backend';
    /**#@-*/

    /**
     * @var string
     */
    protected $section = self::FrontendSetion;

    /**
     * @var array
     */
    protected $messageTemplates = array(
        self::ConfigurationError
            => "Widget options don’t contain '%value%' section.",
    );

    /**
     * @param array $options
     */
    public function __construct($options = array())
    {
        if (! is_array($options)) {
            $options = func_get_args();
            $temp = array();
            if (! empty($options)) {
                $temp['section'] = array_shift($options);
            }
            $options = $temp;
        }
        parent::__construct($options);
    }

    /**
     * @param string $section
     */
    public function setSection($section)
    {
        switch (true) {
            case ($section === self::FrontendSetion):
            case ($section === self::BackendSection):
                $temp['section'] = $section;
                break;
        }
    }

    /**
     * @param array $widget
     * @return boolean
     */
    public function isValid($widget)
    {
        if (! isset($widget['invokables'][$this->section]['controller'])) {
            $this->setValue(self::Controller);
            $this->error(self::ConfigurationError);
            return false;
        }
        if (! isset($widget['invokables'][$this->section]['action'])) {
            $this->setValue(self::Action);
            $this->error(self::ConfigurationError);
            return false;
        }
        return true;
    }
}
