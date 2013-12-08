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

use Zend\Db\Adapter\AdapterInterface,
    Zend\Validator\Db\AbstractDb,
    Zend\Form\Form;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class CategoryForm extends Form
{
    /**
     * @var Zend\Db\Adapter\AdapterInterface
     */
    protected $adapter;

    /**
     * @return void
     */
    public function __construct(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;

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
        $spec = $this->getInputFilter();

        $spec->add([
            'name' => 'title',
            'required' => true,
            'filters' => [
                [
                    'name' => 'StringTrim',
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
            'name' => 'name',
            'required' => true,
            'filters' => [
                [
                    'name' => 'StringTrim',
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
                [
                    'name' => 'DbNoRecordExists',
                    'options' => [
                        'adapter' => $this->adapter,
                        'table' => 'sc_content',
                        'field' => 'name',
                        'messages' => [
                            AbstractDb::ERROR_RECORD_FOUND => "The name '%value%' already exists.",
                        ],
                    ],
                ],
            ],
        ]);

        return $this;
    }
}
