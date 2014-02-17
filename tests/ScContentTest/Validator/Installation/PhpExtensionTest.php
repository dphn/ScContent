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

use ScContent\Validator\Installation\PhpExtension,
    //
    PHPUnit_Framework_TestCase;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 *
 * @coversDefaultClass \ScContent\Validator\Installation\PhpExtension
 */
class PhpExtensionTest extends PHPUnit_Framework_TestCase
{
   /**
    * @var ScContent\Validator\Installation\PhpExtension
    */
    protected $validator;

    /**
     * @return void
     */
    protected function setUp()
    {
        $this->validator = new PhpExtension();
    }

    /**
     * @covers ::setCallback
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
     * @covers ::getCallback
     */
    public function testCallbackGetter()
    {
        $validator = $this->validator;
        $callback = function() {};
        $validator->setCallback($callback);
        $this->assertEquals($callback, $validator->getCallback());
    }

    /**
     * @covers ::getValueFromCallback
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
     * @covers ::isValid
     */
    public function testIsValidMissingNameOption()
    {
        $validator = $this->validator;
        $options = [];
        $this->setExpectedException(
            'Zend\Validator\Exception\InvalidArgumentException'
        );
        $validator->isValid($options);
    }

    /**
     * @covers ::isValid
     */
    public function testIsValidIfExtensionNotLoaded()
    {
        $validator = $this->validator;

        // fake extension_loaded()
        $callback = function() {
            return false;
        };

        $validator->setCallback($callback);
        $options = [
            'name' => 'fake_php_extension_name',
        ];
        $this->assertFalse($validator->isValid($options));
    }

    /**
     * @covers ::isValid
     */
    public function testIsValidIfExtensionLoaded()
    {
        $validator = $this->validator;

        // fake extension_loaded()
        $callback = function() {
            return true;
        };

        $validator->setCallback($callback);
        $options = [
            'name' => 'fake_php_extension_name',
        ];
        $this->assertTrue($validator->isValid($options));
    }
}
