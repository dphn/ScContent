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

use Zend\InputFilter\InputFilterProviderInterface,
    Zend\Form\Fieldset;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class FileEditFieldset extends Fieldset implements InputFilterProviderInterface
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct('file');

        $this->add([
            'name' => 'title',
            'attributes' => [
                'placeholder' => 'Title',
                'class' => 'form-control input-lg',
            ],
        ]);

        $this->add([
            'name' => 'content',
            'options' => [
                'label' => 'Alternative text',
                'label_attributes' => [
                    'class' => 'label-default'
                ],
            ],
            'attributes' => [
                'id' => 'content',
                'class' => 'form-control',
            ],
        ]);

        $this->add([
            'name' => 'description',
            'type' => 'textarea',
            'options' => [
                'label' => 'Description',
                'label_attributes' => [
                    'class' => 'label-default'
                ],
            ],
            'attributes' => [
                'id' => 'description',
                'class' => 'form-control',
                'rows' => 4,
            ],
        ]);
    }

    /**
     * @return array
     */
    public function getInputFilterSpecification()
    {
        return [
            'title' => [
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
            ],
        ];
    }
}
