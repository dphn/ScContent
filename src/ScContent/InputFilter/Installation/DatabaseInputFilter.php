<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013-2014 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\InputFilter\Installation;

use Zend\InputFilter\InputFilter;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class DatabaseInputFilter extends InputFilter
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->add([
            'name' => 'driver',
            'required' => true,
            'validators' => [
                [
                    'name' => 'ScContent\Validator\Db\Connection',
                ],
            ],
        ]);

        $this->add([
            'name' => 'path',
            'required' => false,
            'filters' => [
                [
                    'name' => 'StringTrim',
                ],
            ],
        ]);

        $this->add([
            'name' => 'database',
            'required' => false,
            'filters' => [
                [
                    'name' => 'StringTrim',
                ],
            ],
        ]);

        $this->add([
            'name' => 'host',
            'required' => false,
            'filters' => [
                [
                    'name' => 'StringTrim',
                ],
            ],
        ]);

        $this->add([
            'name' => 'username',
            'required' => false,
            'filters' => [
                [
                    'name' => 'StringTrim',
                ],
            ],
        ]);

        $this->add([
            'name' => 'password',
            'required' => false,
            'filters' => [
                [
                    'name' => 'StringTrim',
                ],
            ],
        ]);

        $this->add([
            'name' => 'password2',
            'required' => false,
            'filters' => [
                [
                    'name' => 'StringTrim',
                ],
            ],
            'validators' => [
                [
                    'name' => 'identical',
                    'options' => ['token' => 'password'],
                ],
            ],
        ]);
    }
}
