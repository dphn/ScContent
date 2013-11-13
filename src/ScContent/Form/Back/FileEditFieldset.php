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

use Zend\InputFilter\InputFilterProviderInterface,
    Zend\Form\Fieldset;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class FileEditFieldset extends Fieldset
    implements InputFilterProviderInterface
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct('file');

        $this->add(array(
            'name' => 'title',
            'attributes' => array(
                'placeholder' => 'Title',
                'class' => 'form-control input-lg',
            ),
        ));

        $this->add(array(
            'name' => 'content',
            'options' => array(
                'label' => 'Alternative text',
                'label_attributes' => array(
                    'class' => 'content-form-label'
                ),
            ),
            'attributes' => array(
                'id' => 'content',
                'class' => 'form-control',
            ),
        ));

        $this->add(array(
            'name' => 'description',
            'type' => 'textarea',
            'options' => array(
                'label' => 'Description',
                'label_attributes' => array(
                    'class' => 'content-form-label'
                ),
            ),
            'attributes' => array(
                'id' => 'description',
                'class' => 'form-control',
                'rows'  => 4,
            ),
        ));
    }

    /**
     * @return array
     */
    public function getInputFilterSpecification()
    {
        return array(
            'title' => array(
                'required' => true,
            ),
        );
    }
}
