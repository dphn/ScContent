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

use Zend\Form\Form,
    Zend\Validator\Db\NoRecordExists,
    //
    DateTimeZone,
    Locale;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class AccountForm extends Form
{
    /**
     * @var Zend\Validator\Db\NoRecordExists
     */
    protected $usernameValidator;

    /**
     * @var Zend\Validator\Db\NoRecordExists
     */
    protected $emailValidator;

    /**
     * @var array
     */
    protected $dateTimeZoneValueOptions = [];

    /**
     * @var array
     */
    protected $locales = [];

    /**
     * Constructor
     */
    public function __construct(NoRecordExists $usernameValidator, NoRecordExists $emailValidator)
    {
        $this->usernameValidator = $usernameValidator;
        $this->emailValidator = $emailValidator;

        $identifiers = DateTimeZone::listIdentifiers();
        $array = [];
        foreach ($identifiers as $identifier) {
            $pos = strrpos($identifier, '/');
            if (false === $pos) {
                $timeZoneLocation = $timeZoneName = $identifier;
            } else {
                $timeZoneLocation = substr($identifier, 0, $pos);
                $timeZoneName = substr($identifier, $pos + 1);
            }
            $array[$timeZoneLocation][$identifier] = $timeZoneName;
        }
        foreach ($array as $k => $v) {
            $options = [];
            foreach ($v as $value => $label) {
                $options[] = ['value' => $value, 'label' => $label];
            }
            $this->dateTimeZoneValueOptions[] = [
                'label' => $k,
                'options' => $options,
            ];
        }

        $this->locales = include __DIR__ . str_replace(
            '/', DS, '/../locales.inc.php'
        );

        parent::__construct('account');
        $this->setFormSpecification()->setInputSpecification();
    }

    /**
     * @return AccountForm
     */
    protected function setFormSpecification()
    {
        $this->add([
            'name' => 'locale',
            'type' => 'select',
            'options' => [
                'label' => 'Please select your language and region',
                'value_options' => $this->locales,
            ],
        ]);

        $this->add([
            'name' => 'timezone',
            'type' => 'select',
            'options' => [
                'label' => 'Please select your timezone',
                'value_options' => $this->dateTimeZoneValueOptions,
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
            'name' => 'email',
            'type' => 'email',
            'options' => [
                'label' => 'Email',
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
     * @return AccountForm
     */
    protected function setInputSpecification()
    {
        $spec = $this->getInputFilter();

        $spec->add([
            'name' => 'locale',
            'required' => true,
        ]);

        $spec->add([
            'name' => 'timezone',
            'required' => true,
        ]);

        $spec->add([
            'name' => 'username',
            'required' => true,
            'validators' => [
                [
                    'name' => 'StringLength',
                    'options' => [
                        'min' => 3,
                        'max' => 255,
                    ],
                ],
                $this->usernameValidator,
            ],
        ]);

        $spec->add([
            'name' => 'email',
            'required' => true,
            'validators' => [
                [
                    'name' => 'EmailAddress',
                ],
                $this->emailValidator,
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
                ]
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
                    'name' => 'StringLength',
                    'options' => [
                        'min' => 6,
                    ],
                ],
                [
                    'name' => 'Identical',
                    'options' => [
                        'token' => 'password',
                    ],
                ],
            ],
        ]);

        return $this;
    }
}
