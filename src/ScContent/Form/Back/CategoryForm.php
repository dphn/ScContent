<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Form\Back;

use Zend\InputFilter\InputFilter,
    Zend\Form\Form;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class CategoryForm extends Form
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct('category');
        $this->setFormSpecification()->setInputSpecification();
    }

    /**
     * @return CategoryForm
     */
    protected function setFormSpecification()
    {
        $this->add([
            'name' => 'title',
            'type' => 'text',
            'attributes' => [
                'class' => 'form-control input-lg',
                'placeholder' => 'Title',
            ],
        ]);

        $this->add([
            'name' => 'name',
            'type' => 'text',
            'attributes' => [
                'placeholder' => 'Name',
                'required'    => 'required',
                'class'       => 'form-control input-sm',
            ],
            'options' => [
            'label' => 'Category Name',
            ],
        ]);

        $this->add([
            'name' => 'description',
            'type' => 'textarea',
            'options' => [
                'label' => 'Category description',
                'label_attributes' => [
                    'class' => 'content-form-label'
                ],
            ],
            'attributes' => [
                'id' => 'description',
                'class' => 'form-control',
                'rows'  => 14,
            ],
        ]);

        $this->add([
            'name' => 'status',
            'type' => 'select',
            'options' => [
                'label' => 'Status',
                'value_options' => [
                    'published' => 'Published',
                    'draft' => 'Draft',
                ],
            ],
            'attributes' => [
                'id' => 'status',
                'class' => 'form-control',
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
     * @return CategoryForm
     */
    protected function setInputSpecification()
    {
        $spec = new InputFilter();

        $spec->add([
            'name' => 'title',
            'required' => true,
        ]);

        $this->setInputFilter($spec);

        return $this;
    }
}
