<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013-2014 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Form\Installation;

use Zend\Form\Form;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class RegistrationForm extends Form
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct('register');

        $this->setFormSpecification()->setInputSpecification();
    }

    /**
     * @return RegisterForm
     */
    protected function setFormSpecification()
    {
        $this->add([
            'name' => 'username',
            'type' => 'text',
            'options' => [
                'label' => 'Username',
            ],
        ]);

        $this->add([
            'name' => 'password',
            'type' => 'password',
            'options' => [
                'label' => 'Password',
            ],
        ]);

        $this->add([
            'name' => 'password2',
            'type' => 'password',
            'options' => [
                'label' => 'Password verify',
            ],
        ]);

        $this->add([
            'name' => 'submit',
            'type' => 'submit',
            'attributes' => [
                'value' => 'Submit',
                'class' => 'btn-primary',
            ],
        ]);

        return $this;
    }

    /**
     * @return RegisterForm
     */
    protected function setInputSpecification()
    {
        $spec = $this->getInputFilter();

        $spec->add([
            'name' => 'username',
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
                        'min' => 4,
                        'max' => 255,
                    ],
                ],
            ],
        ]);

        $spec->add([
            'name' => 'password',
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
                        'min' => 6,
                    ],
                ],
            ],
        ]);

        $spec->add([
            'name' => 'password2',
            'required' => true,
            'filters' => [
                [
                    'name' => 'StringTrim',
                ],
            ],
            'validators' => [
                [
                    'name' => 'identical',
                    'options' => [
                        'token' => 'password',
                    ],
                ],
            ],
        ]);

        return $this;
    }
}
