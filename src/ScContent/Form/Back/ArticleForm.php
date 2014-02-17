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

use Zend\Db\Adapter\AdapterInterface,
    Zend\Validator\Db\AbstractDb,
    Zend\Form\Form;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class ArticleForm extends Form
{
    /**
     * @var \Zend\Db\Adapter\AdapterInterface
     */
    protected $adapter;

    /**
     * Constructor
     *
     * @param \Zend\Db\Adapter\AdapterInterface $adapter
     */
    public function __construct(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;

        parent::__construct('article');

        $this->setAttribute('method', 'post')
            ->setFormSpecification()
            ->setInputSpecification();
    }

    /**
     * @param  object $object
     * @param  int $flags
     * @return ArticleForm
     */
    public function bind($object, $flags = Form::VALUES_NORMALIZED)
    {
        $inputFilter = $this->getInputFilter();
        $input = $inputFilter->get('name');
        $validatorsChain = $input->getValidatorChain();
        foreach ($validatorsChain->getValidators() as $validation) {
            if (! isset($validation['instance'])) {
                continue;
            }
            $validator = $validation['instance'];
            if ($validator instanceof AbstractDb) {
                $validator->setExclude([
                    'field' => 'id',
                    'value' => $object->getId(),
                ]);
            }
        }

        parent::bind($object, $flags);
        return $this;
    }

    /**
     * @return ArticleForm
     */
    protected function setFormSpecification()
    {
        $this->add([
            'name' => 'title',
            'type' => 'text',
            'attributes' => [
                'placeholder' => 'Title',
                'required'    => 'required',
                'class'       => 'form-control input-lg',
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
                'label' => 'Article Name',
            ],
        ]);

        $this->add([
            'name' => 'content',
            'type' => 'textarea',
            'options' => [
                'label' => 'Article content',
                'label_attributes' => [
                    'class' => 'label-default'
                ],
            ],
            'attributes' => [
                'id' => 'content',
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
     * @return ArticleForm
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
                            AbstractDb::ERROR_RECORD_FOUND
                                => "The name '%value%' already exists.",
                        ],
                    ],
                ],
            ],
        ]);

        return $this;
    }
}
