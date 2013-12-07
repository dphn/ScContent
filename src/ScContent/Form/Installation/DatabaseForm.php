<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Form\Installation;

use ScContent\InputFilter\Back\DatabaseInputFilter,
    //
    Zend\Form\Form;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class DatabaseForm extends Form
{
    /**
     * @var array
     */
    protected $drivers = [];

    /**
     * Constructor
     *
     * @param array $drivers
     */
    public function __construct(array $drivers)
    {
        parent::__construct('database');
        $this->drivers = array_combine($drivers, $drivers);
        $this->setFormSpecification()->setInputFilter(new DatabaseInputFilter());
    }

    /**
     * @return ScContent\Form\Installation\DatabaseForm
     */
    protected function setFormSpecification()
    {
        $this->add([
            'name' => 'driver',
            'type' => 'select',
            'options' => [
                'label' => 'Database driver',
                'value_options' => $this->drivers,
            ],
            'attributes' => [
                'id' => 'driver',
            ],
        ]);

        $this->add([
            'name' => 'path',
            'attributes' => [
                'type' => 'text',
                'id' => 'dbpath',
            ],
            'options' => [
                'label' => 'Path (only for SQLite)'
            ],
        ]);

        $this->add([
            'name' => 'host',
            'attributes' => [
                'type' => 'text',
                'id' => 'host',
                'value' => 'localhost',
            ],
            'options' => [
                'label' => 'Host',
            ],
        ]);

        $this->add([
            'name' => 'database',
            'attributes' => [
                'type' => 'text',
                'id' => 'database',
            ],
            'options' => [
                'label' => 'Database name',
            ],
        ]);

        $this->add([
            'name' => 'username',
            'attributes' => [
                'type' => 'text',
                'id' => 'username',
            ],
            'options' => [
                'label' => 'Username',
            ],
        ]);

        $this->add([
            'name' => 'password',
            'attributes' => [
                'type' => 'password',
                'id' => 'password',
            ],
            'options' => [
                'label' => 'Password',
            ],
        ]);

        $this->add([
            'name' => 'password2',
            'attributes' => [
                'type' => 'password',
                'id' => 'password2',
            ],
            'options' => [
                'label' => 'Password verify',
            ],
        ]);

        $this->add([
            'name' => 'submit',
            'attributes' => [
                'type' => 'submit',
                'value' => 'Install',
                'id' => 'submit',
                'class' => 'btn-primary',
            ],
        ]);

        return $this;
    }
}
