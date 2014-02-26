<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013-2014 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContentTest\Controller;

use ScContent\Controller\AbstractInstallation,
    ScContent\Options\Installation\Installation,
    ScContent\Options\Installation\Member,
    ScContent\Options\Installation\Step,
    //
    Zend\Mvc\Router\Http\TreeRouteStack as HttpRouter,
    Zend\Mvc\MvcEvent,
    //
    ScContentTest\Bootstrap,
    //
    PHPUnit_Framework_TestCase;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 *
 * @coversDefaultClass \ScContent\Controller\AbstractInstallation
 */
class AbstractInstallationTest extends PHPUnit_Framework_TestCase
{
    protected $serviceManager;
    protected $validatorManager;

    protected function setUp()
    {
        $this->serviceManager = Bootstrap::getServiceManager();
        $this->validatorManager = $this->serviceManager->get(
            'ValidatorManager'
        );
    }

    /**
     * @covers ::setInstallation
     */
    public function testSetInstallation()
    {
        $installation = new Installation('test');
        $mock = $this->getMockForAbstractClass(
            'ScContent\Controller\AbstractInstallation'
        );
        $mock->setInstallation($installation);
        $this->assertAttributeEquals($installation, '_installation', $mock);
    }

    /**
     * @covers ::getInstallation
     */
    public function testGetInstallationWhenInstallationWasSetDirectly()
    {
        $installation = new Installation('test');
        $mock = $this->getMockForAbstractClass(
            'ScContent\Controller\AbstractInstallation'
        );
        $mock->setInstallation($installation);
        $this->assertEquals($installation, $mock->getInstallation());
    }

    /**
     * @covers ::getInstallation
     */
    public function testGetInstallationWhenInstallationWasNotSetAndMissingEventParam_installation()
    {
        $mock = $this->getMockForAbstractClass(
            'ScContent\Controller\AbstractInstallation'
        );

        $this->setExpectedException('ScContent\Exception\DomainException');
        $mock->getInstallation();
    }

    /**
     * @covers ::getInstallation
     */
    public function testGetInstallationWhenEventParam_installation_IsNotInstanceOfInstallation()
    {
        $mock = $this->getMockForAbstractClass(
            'ScContent\Controller\AbstractInstallation'
        );
        $event = new MvcEvent();
        $event->setParam('installation', 'unexpected_string_not_instance_of_installation');
        $mock->setEvent($event);

        $this->setExpectedException('ScContent\Exception\DomainException');
        $mock->getInstallation();
    }

    /**
     * @covers ::getInstallation
     */
    public function testGetInstallation()
    {
        $installation = new Installation('test');
        $mock = $this->getMockForAbstractClass(
            'ScContent\Controller\AbstractInstallation'
        );
        $event = new MvcEvent();
        $event->setParam('installation', $installation);
        $mock->setEvent($event);

        $this->assertEquals($installation, $mock->getInstallation());
        $this->assertAttributeEquals($installation, '_installation', $mock);
    }

    /**
     * @covers ::getRedirect
     */
    public function testGetRedirect()
    {
        $event     = new MvcEvent();
        $routeName = 'fake_route';
        $routeUrl  = '/bar';

        $routerConfig = [
            'routes' => [
                $routeName => [
                    'type'  => 'literal',
                    'options' => [
                        'route' => $routeUrl,
                    ],
                ],
            ],
        ];
        $router = HttpRouter::factory($routerConfig);
        $event->setRouter($router);

        $installation = new Installation('test');
        $installation->setRedirectOnSuccess($routeName);
        $event->setParam('installation', $installation);

        $mock = $this->getMockForAbstractClass(
            'ScContent\Controller\AbstractInstallation'
        );
        $mock->setEvent($event);

        $this->assertEquals($routeUrl, $mock->getRedirect());
    }

    /**
     * @covers ::setValidator
     */
    public function testSetValidator()
    {
        $validator = $this->getMockForAbstractClass(
            'Zend\Validator\AbstractValidator'
        );

        $mock = $this->getMockForAbstractClass(
            'ScContent\Controller\AbstractInstallation'
        );

        $mock->setValidator($validator);
        $this->assertAttributeEquals($validator, '_validator', $mock);
    }

    /**
     * @covers ::getValidator
     */
    public function testGetValidatorWhenValidatorWasSetDirectly()
    {
        $validator = $this->getMockForAbstractClass(
            'Zend\Validator\AbstractValidator'
        );

        $mock = $this->getMockForAbstractClass(
            'ScContent\Controller\AbstractInstallation'
        );

        $mock->setValidator($validator);

        $this->assertEquals($validator, $mock->getValidator());
    }

    /**
     * @covers ::getValidator
     */
    public function testGetValidator()
    {
        $validatorName = 'fake_validator_name_95254732';
        $validator = $this->getMockForAbstractClass(
            'Zend\Validator\AbstractValidator'
        );
        $validatorManager = $this->validatorManager;
        $validatorManager->setFactory(
            $validatorName,
            function() use ($validator) {
                return $validator;
            }
        );

        $installation = new Installation('test_installation');
        $step = new Step('test_step');
        $options = [
            'validator' => $validatorName,
            'service'   => 'fake_service',
        ];
        $member = new Member('test_member', $options);
        $step->addMember($member);
        $step->setCurrentMemberName('test_member');
        $installation->addStep($step);
        $installation->setCurrentStepName('test_step');


        $mock = $this->getMockForAbstractClass(
            'ScContent\Controller\AbstractInstallation'
        );
        $mock->setServiceLocator($this->serviceManager);
        $mock->setInstallation($installation);
        $this->assertEquals($validator, $mock->getValidator());
    }

    /**
     * @covers ::setService
     */
    public function testSetService()
    {
        $service = $this->getMockForAbstractClass(
            'ScContent\Service\Installation\AbstractInstallationService'
        );

        $mock = $this->getMockForAbstractClass(
            'ScContent\Controller\AbstractInstallation'
        );

        $mock->setService($service);

        $this->assertAttributeEquals($service, '_service', $mock);
    }

    /**
     * @covers ::getService
     */
    public function testGetServiceWhenServiceIsNotInstanceOf_AbstractInstallationService()
    {
        $serviceName = 'fake_service_name_34814567';
        $service = new \StdClass(); // or other ...
        $serviceManager = $this->serviceManager;
        $serviceManager->setFactory(
            $serviceName,
            function() use ($service) {
                return $service;
            }
        );

        $installation = new Installation('test_installation');
        $step = new Step('test_step');
        $options = [
            'validator' => 'fake_validator',
            'service'   => $serviceName,
        ];
        $member = new Member('test_member', $options);
        $step->addMember($member);
        $step->setCurrentMemberName('test_member');
        $installation->addStep($step);
        $installation->setCurrentStepName('test_step');


        $mock = $this->getMockForAbstractClass(
            'ScContent\Controller\AbstractInstallation'
        );
        $mock->setServiceLocator($serviceManager);
        $plugin = $this
            ->getMockBuilder('ScContent\Controller\Plugin\TranslatorProxy')
            ->disableOriginalConstructor()
            ->setMethods(['__invoke'])
            ->getMock();

        $pluginManager = $mock->getPluginManager();
        $pluginManager->setFactory(
            'scTranslate',
            function() use ($plugin) {
                return $plugin;
            }
        );
        $mock->setInstallation($installation);

        $this->setExpectedException('ScContent\Exception\DomainException');
        $mock->getService();
    }

    /**
     * @covers ::getService
     */
    public function testGetService()
    {
        $serviceName = 'fake_service_name_32657145';
        $service = $this->getMockForAbstractClass(
            'ScContent\Service\Installation\AbstractInstallationService'
        );
        $serviceManager = $this->serviceManager;
        $serviceManager->setFactory(
            $serviceName,
            function() use ($service) {
                return $service;
            }
        );

        $installation = new Installation('test_installation');
        $step = new Step('test_step');
        $options = [
            'validator' => 'fake_validator',
            'service'   => $serviceName,
        ];
        $member = new Member('test_member', $options);
        $step->addMember($member);
        $step->setCurrentMemberName('test_member');
        $installation->addStep($step);
        $installation->setCurrentStepName('test_step');


        $mock = $this->getMockForAbstractClass(
            'ScContent\Controller\AbstractInstallation'
        );
        $mock->setServiceLocator($serviceManager);
        $mock->setInstallation($installation);

        $this->assertEquals($service, $mock->getService());
    }
}
