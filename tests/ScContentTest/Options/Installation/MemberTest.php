<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013-2014 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContentTest\Options\Installation;

use ScContent\Options\Installation\Member,
    //
    PHPUnit_Framework_TestCase;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 *
 * @coversDefaultClass \ScContent\Options\Installation\Member
 */
class MemberTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers ::__construct
     */
    public function testConstructorSetMemberName()
    {
        $memberName    = 'fake_member_name';
        $validatorName = 'fake_validator_name';
        $serviceName   = 'fake_service_name';
        $options = [
            'validator' => $validatorName,
            'service'   => $serviceName,
        ];
        $member = new Member($memberName, $options);
        $this->assertAttributeEquals($memberName, 'name', $member);
    }

    /**
     * @covers ::__construct
     */
    public function testConstructorWithoutValidatorName()
    {
        $serviceName = 'fake_service_name';
        $options = [
            'service' => $serviceName,
        ];
        $this->setExpectedException(
            'ScContent\Exception\DomainException'
        );
        $member = new Member('test', $options);
    }

    /**
     * @covers ::__construct
     */
    public function testConstructorWithoutServiceOrControllerName()
    {
        $validatorName = 'fake_validator_name';
        $options = [
            'validator' => $validatorName,
        ];
        $this->setExpectedException(
            'ScContent\Exception\DomainException'
        );
        $member = new Member('test', $options);
    }

    /**
     * @covers ::__construct
     */
    public function testConstructorSetValidatorFromOptions()
    {
        $validatorName = 'fake_validator_name';
        $serviceName   = 'fake_service_name';
        $options = [
            'validator' => $validatorName,
            'service'   => $serviceName,
        ];
        $member = new Member('test', $options);
        $this->assertAttributeEquals($validatorName, 'validator', $member);
    }

    /**
     * @covers ::__construct
     */
    public function testConstructorSetControllerFromOptions()
    {
        $validatorName  = 'fake_validator_name';
        $controllerName = 'fake_controller_name';
        $options = [
            'validator'  => $validatorName,
            'controller' => $controllerName,
        ];
        $member = new Member('test', $options);
        $this->assertAttributeEquals($controllerName, 'controller', $member);
    }

    /**
     * @covers ::__construct
     */
    public function testConstructorSetActionFromOptions()
    {
        $validatorName  = 'fake_validator_name';
        $controllerName = 'fake_controller_name';
        $actionName     = 'fake_action_name';
        $options = [
            'validator'  => $validatorName,
            'controller' => $controllerName,
            'action'     => $actionName,
        ];
        $member = new Member('test', $options);
        $this->assertAttributeEquals($actionName, 'action', $member);
    }

    /**
     * @covers ::__construct
     */
    public function testConstructorSetServiceFromOptions()
    {
        $validatorName = 'fake_validator_name';
        $serviceName   = 'fake_service_name';
        $options = [
            'validator' => $validatorName,
            'service'   => $serviceName,
        ];
        $member = new Member('test', $options);
        $this->assertAttributeEquals($serviceName, 'service', $member);
    }

    /**
     * @covers ::__construct
     */
    public function testConstructorSetBatchFromOptions()
    {
        $validatorName = 'fake_validator_name';
        $serviceName   = 'fake_service_name';
        $batch = [
            [
                'fake_validator_option_name' => 'fake_validator_option_value',
                'fake_service_option_name'   => 'fake_service_option_value',
                // ...
            ]
        ];
        $options = [
            'validator' => $validatorName,
            'service'   => $serviceName,
            'batch'     => $batch,
        ];
        $member = new Member('test', $options);
        $this->assertAttributeEquals($batch, 'items', $member);
    }

    /**
     * @covers ::getName
     */
    public function testGetMemberName()
    {
        $memberName    = 'fake_member_name';
        $validatorName = 'fake_validator_name';
        $serviceName   = 'fake_service_name';
        $options = [
            'validator' => $validatorName,
            'service'   => $serviceName,
        ];
        $member = new Member($memberName, $options);
        $this->assertEquals($memberName, $member->getName());
    }

    /**
     * @covers ::setValidator
     */
    public function testSetValidator()
    {
        $validatorName = 'fake_validator_name';
        $serviceName   = 'fake_service_name';
        $options = [
            'validator' => $validatorName,
            'service'   => $serviceName,
        ];
        $member = new Member('test', $options);

        $newValidatorName = 'new_validator_name';
        $member->setValidator($newValidatorName);
        $this->assertAttributeEquals(
            $newValidatorName,
            'validator',
            $member
        );
    }

    /**
     * @covers ::getValidator
     */
    public function testGetValidator()
    {
        $validatorName = 'fake_validator_name';
        $serviceName   = 'fake_service_name';
        $options = [
            'validator' => $validatorName,
            'service'   => $serviceName,
        ];
        $member = new Member('test', $options);
        $this->assertEquals($validatorName, $member->getValidator());
    }

    /**
     * @covers ::setService
     */
    public function testSetService()
    {
        $validatorName = 'fake_validator_name';
        $serviceName   = 'fake_service_name';
        $options = [
            'validator' => $validatorName,
            'service'   => $serviceName,
        ];
        $member = new Member('test', $options);

        $newServiceName = 'new_service_name';
        $member->setService($newServiceName);
        $this->assertAttributeEquals(
            $newServiceName,
            'service',
            $member
        );
    }

    /**
     * @covers ::getService
     */
    public function testGetService()
    {
        $validatorName = 'fake_validator_name';
        $serviceName   = 'fake_service_name';
        $options = [
            'validator' => $validatorName,
            'service'   => $serviceName,
        ];
        $member = new Member('test', $options);
        $this->assertEquals($serviceName, $member->getService());
    }

    /**
     * @covers ::setController
     */
    public function testSetController()
    {
        $validatorName = 'fake_validator_name';
        $serviceName   = 'fake_service_name';
        $options = [
            'validator' => $validatorName,
            'service'   => $serviceName,
        ];
        $member = new Member('test', $options);

        $controllerName = 'fake_controller_name';
        $member->setController($controllerName);
        $this->assertAttributeEquals(
            $controllerName,
            'controller',
            $member
        );
    }

    /**
     * @covers ::getController
     */
    public function testGetController()
    {
        $validatorName  = 'fake_validator_name';
        $controllerName = 'fake_controller_name';
        $options = [
            'validator'  => $validatorName,
            'controller' => $controllerName,
        ];
        $member = new Member('test', $options);
        $this->assertEquals($controllerName, $member->getController());
    }

    /**
     * @covers ::setAction
     */
    public function testSetAction()
    {
        $validatorName  = 'fake_validator_name';
        $controllerName = 'fake_controller_name';
        $options = [
            'validator'  => $validatorName,
            'controller' => $controllerName,
        ];
        $member = new Member('test', $options);

        $actionName = 'fake_action_name';
        $member->setAction($actionName);
        $this->assertAttributeEquals(
            $actionName,
            'action',
            $member
        );
    }

    /**
     * @covers ::getAction
     */
    public function testGetAction()
    {
        $validatorName  = 'fake_validator_name';
        $controllerName = 'fake_controller_name';
        $actionName     = 'fake_action_name';
        $options = [
            'validator'  => $validatorName,
            'controller' => $controllerName,
            'action'     => $actionName,
        ];
        $member = new Member('test', $options);
        $this->assertEquals($actionName, $member->getAction());
    }

    /**
     * @covers ::setBatch
     */
    public function testSetBatchWhenBatchIsNotArray()
    {
        $validatorName = 'fake_validator_name';
        $serviceName   = 'fake_service_name';
        $options = [
            'validator' => $validatorName,
            'service'   => $serviceName,
        ];
        $member = new Member('test', $options);

        $batch = null;
        $this->setExpectedException(
            'ScContent\Exception\InvalidArgumentException'
        );
        $member->setBatch($batch);
    }

    /**
     * @covers ::setBatch
     */
    public function testSetBatchWhenBatchIsEmpty()
    {
        $validatorName = 'fake_validator_name';
        $serviceName   = 'fake_service_name';
        $options = [
            'validator' => $validatorName,
            'service'   => $serviceName,
        ];
        $member = new Member('test', $options);

        $batch = [];
        $this->setExpectedException(
            'ScContent\Exception\InvalidArgumentException'
        );
        $member->setBatch($batch);
    }

    /**
     * @covers ::setBatch
     */
    public function testSetBatch()
    {
        $validatorName = 'fake_validator_name';
        $serviceName   = 'fake_service_name';
        $options = [
            'validator' => $validatorName,
            'service'   => $serviceName,
        ];
        $member = new Member('test', $options);

        $batch = [
            [
                'fake_validator_option_name' => 'fake_validator_option_value',
                'fake_service_option_name'   => 'fake_service_option_value',
                // ...
            ]
        ];
        $member->setBatch($batch);
        $this->assertAttributeEquals($batch, 'items', $member);
    }

    /**
     * @covers ::getBatch
     */
    public function testGetBatch()
    {
        $validatorName = 'fake_validator_name';
        $serviceName   = 'fake_service_name';
        $batch = [
            [
                'fake_validator_option_name' => 'fake_validator_option_value',
                'fake_service_option_name'   => 'fake_service_option_value',
                // ...
            ]
        ];
        $options = [
            'validator' => $validatorName,
            'service'   => $serviceName,
            'batch'     => $batch,
        ];
        $member = new Member('test', $options);
        $this->assertEquals($batch, $member->getBatch());
    }
}
