<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013-2014 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Form\Back;

use Zend\Form\Form;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class WidgetConfigurationForm extends Form
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct('widget_configuration');

        $this->setAttribute('method', 'post')
            ->setFormSpecification()
            ->setInputSpecification();
    }

    /**
     * @param object $object
     * @param  int $flags
     * @return WidgetConfigurationForm
     */
    public function bind($object, $flags = Form::VALUES_NORMALIZED)
    {
        $this->add([
            'name' => 'roles',
            'type' => 'multicheckbox',
            'options' => [
                'label' => 'Applicable roles',
                'label_attributes' => [
                    'class' => 'checkbox',
                ],
                'value_options' => array_combine(
                    $object->getRoles(),
                    $object->getRoles()
                ),

            ],
            'attributes' => [
                'value' => $object->findApplicableRoles(),
            ],
        ]);

        $spec = $this->getInputFilter();

        $spec->add([
            'name' => 'roles',
            'required' => false,
        ]);

        parent::bind($object, $flags);
        return $this;
    }

    /**
     * @return WidgetConfigurationForm
     */
    protected function setFormSpecification()
    {
        $this->add([
            'name' => 'display_name',
            'type' => 'text',
            'options' => [
                'label' => 'Display name',
                'label_attributes' => [
                    'class' => 'label-default',
                ],
            ],
            'attributes' => [
                'required' => 'required',
                'class' => 'form-control',
                'id' => 'display_name',
            ],
        ]);

        $this->add([
            'name' => 'description',
            'type' => 'textarea',
            'options' => [
                'label' => 'Description',
                'label_attributes' => [
                    'class' => 'label-default',
                ],
            ],
            'attributes' => [
                'class' => 'form-control',
                'id' => 'description',
                'rows' => 8,
            ],
        ]);

        $this->add([
            'name' => 'save',
            'type' => 'button',
            'options' => [
                'label' => 'Save',
            ],
            'attributes' => [
                'type'  => 'submit',
                'class' => 'btn btn-primary',
                'value' => 'save',
            ],
        ]);

        return $this;
    }

    /**
     * @return WidgetConfigurationForm
     */
    protected function setInputSpecification()
    {
        $spec = $this->getInputFilter();

        $spec->add([
            'name' => 'display_name',
            'required' => true,
            'required' => false,
            'filters' => [
                [
                    'name' => 'StringTrim',
                ],
                [
                    'name' => 'StripTags',
                ],
            ],
            'validators' => [
                [
                    'name' => 'StringLength',
                    'options' => [
                        'max' => 255,
                        'encoding' => 'utf-8',
                    ],
                ],
            ],
        ]);

        $spec->add([
            'name' => 'description',
            'required' => false,
            'filters' => [
                [
                    'name' => 'StringTrim',
                ],
                [
                    'name' => 'StripTags',
                ],
            ],
        ]);

        return $this;
    }
}
