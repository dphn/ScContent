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
    protected $drivers = array();

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
        $this->add(array(
            'name' => 'driver',
            'type' => 'select',
            'options' => array(
                'label' => 'Database driver',
                'value_options' => $this->drivers,
            ),
            'attributes' => array(
                'id' => 'driver',
            ),
        ));

        $this->add(array(
            'name' => 'path',
            'attributes' => array(
                'type' => 'text',
                'id'   => 'dbpath',
            ),
            'options' => array(
                'label' => 'Path (only for SQLite)'
            ),
        ));

        $this->add(array(
            'name' => 'host',
            'attributes' => array(
                'type'  => 'text',
                'id'    => 'host',
                'value' => 'localhost',
            ),
            'options' => array(
                'label' => 'Host',
            ),
        ));

        $this->add(array(
            'name' => 'database',
            'attributes' => array(
                'type' => 'text',
                'id'   => 'database',
            ),
            'options' => array(
                'label' => 'Database name',
            ),
        ));

        $this->add(array(
            'name' => 'username',
            'attributes' => array(
                'type' => 'text',
                'id'   => 'username',
            ),
            'options' => array(
                'label' => 'Username',
            ),
        ));

        $this->add(array(
            'name' => 'password',
            'attributes' => array(
                'type' => 'password',
                'id'   => 'password',
            ),
            'options' => array(
                'label' => 'Password',
            ),
        ));

        $this->add(array(
            'name' => 'password2',
            'attributes' => array(
                'type' => 'password',
                'id'   => 'password2',
            ),
            'options' => array(
                'label' => 'Password verify',
            ),
        ));

        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type' => 'submit',
                'value' => 'Install',
                'id' => 'submit',
                'class' => 'btn-primary',
            ),
        ));

        return $this;
    }
}
