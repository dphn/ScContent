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

use ScContent\InputFilter\Installation\DatabaseInputFilter,
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
     * @return DatabaseForm
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
        ]);

        $this->add([
            'name' => 'path',
            'type' => 'text',
            'options' => [
                'label' => 'Path (only for SQLite)'
            ],
        ]);

        $this->add([
            'name' => 'host',
            'type' => 'text',
            'attributes' => [
                'value' => 'localhost',
            ],
            'options' => [
                'label' => 'Host',
            ],
        ]);

        $this->add([
            'name' => 'database',
            'type' => 'text',
            'options' => [
                'label' => 'Database name',
            ],
        ]);

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
}
