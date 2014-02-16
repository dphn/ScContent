<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013-2014 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContentTest\Validator\Installation;

use ScContent\Validator\Installation\PhpIni,
    //
    PHPUnit_Framework_TestCase;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class PhpIniTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var ScContent\Validator\Installation\PhpIni
     */
    protected $validator;

    /**
     * @return void
     */
    protected function setUp()
    {
        $this->validator = new PhpIni();
    }

    /**
     * @covers PhpIni::setCallback()
     */
    public function testSetBadCallback()
    {
        $this->setExpectedException(
            'Zend\Validator\Exception\InvalidArgumentException'
        );
        $validator = $this->validator;
        $callback = null;
        $validator->setCallback($callback);
    }

    /**
     * @covers PhpIni::getCallback()
     */
    public function testCallbackGetter()
    {
        $validator = $this->validator;
        $callback = function() {};
        $validator->setCallback($callback);
        $this->assertEquals($callback, $validator->getCallback());
    }

    /**
     * @covers PhpIni::getValueFromCallback()
     */
    public function testCallbackValueGetter()
    {
        $testValue = 'test';
        $validator = $this->validator;
        $callback = function() use ($testValue) {
            return $testValue;
        };
        $validator->setCallback($callback);
        $this->assertEquals($testValue, $validator->getValueFromCallback('fake'));
    }

    /**
     * @covers PhpIni::isValid()
     */
    public function testIsValidMissingNameOption()
    {
        $validator = $this->validator;
        $options = [
            //'name'             => 'fake',
            'validation_type'  => 'expect',
            'validation_value' => 'test',
        ];
        $this->setExpectedException(
            'Zend\Validator\Exception\InvalidArgumentException'
        );
        $validator->isValid($options);
    }

    /**
     * @covers PhpIni::isValid()
     */
    public function testIsValidMissingValidationTypeOption()
    {
        $validator = $this->validator;
        $options = [
            'name'             => 'fake',
            //'validation_type'  => 'expect',
            'validation_value' => 'test',
        ];
        $this->setExpectedException(
            'Zend\Validator\Exception\InvalidArgumentException'
        );
        $validator->isValid($options);
    }

    /**
     * @covers PhpIni::isValid()
     */
    public function testIsValidMissingValidationValueOption()
    {
        $validator = $this->validator;
        $options = [
            'name'             => 'fake',
            'validation_type'  => 'expect',
            //'validation_value' => 'test',
        ];
        $this->setExpectedException(
            'Zend\Validator\Exception\InvalidArgumentException'
        );
        $validator->isValid($options);
    }

    /**
     * @covers PhpIni::isValid()
     */
    public function testNotValidExpect()
    {
        $validator = $this->validator;

        // fake ini_get()
        $callback = function($name) {
            return 'fake_php_ini_param_value';
        };

        $validator->setCallback($callback);
        $options = [
            'name'             => 'fake_php_ini_param_name',
            'validation_type'  => 'expect',
            'validation_value' => 'test',
        ];
        $this->assertFalse($validator->isValid($options));
    }

    /**
     * @covers PhpIni::isValid()
     */
    public function testValidExpect()
    {
        $validator = $this->validator;

        // fake ini_get()
        $callback = function() {
            return 'fake_php_ini_param_value';
        };

        $validator->setCallback($callback);
        $options = [
            'name'             => 'fake_php_ini_param_name',
            'validation_type'  => 'expect',
            'validation_value' => 'fake_php_ini_param_value',
        ];
        $this->assertTrue($validator->isValid($options));
    }

    /**
     * @covers PhpIni::isValid()
     */
    public function testNotValidGreaterThen()
    {
        $validator = $this->validator;

        // fake ini_get()
        $callback = function() {
            return '0';
        };

        $validator->setCallback($callback);
        $options = [
            'name'             => 'fake_php_ini_param_name',
            'validation_type'  => 'greater_then',
            'validation_value' => '100',
        ];
        $this->assertFalse($validator->isValid($options));
    }

    /**
     * @covers PhpIni::isValid()
     */
    public function testNotValidGreaterThenWithNoLimit()
    {
        $validator = $this->validator;

        // fake ini_get()
        $callback = function() {
            return '0';
        };

        $validator->setCallback($callback);
        $options = [
            'name'             => 'fake_php_ini_param_name',
            'validation_type'  => 'greater_then',
            'validation_value' => '100',
            'no_limit'         => '-1',
        ];
        $this->assertFalse($validator->isValid($options));
    }

    /**
     * @covers PhpIni::isValid()
     */
    public function testValidGreateThen()
    {
        $validator = $this->validator;

        // fake ini_get()
        $callback = function() {
            return '100';
        };

        $validator->setCallback($callback);
        $options = [
            'name'             => 'fake_php_ini_param_name',
            'validation_type'  => 'greater_then',
            'validation_value' => '10',
        ];
        $this->assertTrue($validator->isValid($options));
    }

    /**
     * @covers PhpIni::isValid()
     */
    public function testValidGreateThenWithNoLimit()
    {
        $validator = $this->validator;

        // fake ini_get()
        $callback = function() {
            return '-1';
        };

        $validator->setCallback($callback);
        $options = [
            'name'             => 'fake_php_ini_param_name',
            'validation_type'  => 'greater_then',
            'validation_value' => '1000',
            'no_limit'         => '-1',
        ];
        $this->assertTrue($validator->isValid($options));
    }
}
