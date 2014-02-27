<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013-2014 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContentTest\Controller\Back;

use ScContent\Controller\Installation\RequirementsController,
    ScContent\Factory\Options\Installation\InstallationOptionsFactory,
    //
    Zend\Mvc\Router\Http\TreeRouteStack as HttpRouter,
    Zend\Mvc\Router\RouteMatch,
    Zend\Mvc\MvcEvent,
    Zend\Http\Response,
    Zend\Http\Request,
    //
    ScContentTest\Bootstrap,
    //
    PHPUnit_Framework_TestCase;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 *
 * @coversDefaultClass \ScContent\Controller\Installation\RequirementsController
 */
class RequirementsControllerTest extends PHPUnit_Framework_TestCase
{
    protected $controller;
    protected $routeMatch;
    protected $request;
    protected $event;

    /**
     * @return void
     */
    protected function setUp()
    {
        $serviceManager = Bootstrap::getServiceManager();

        $this->controller = new RequirementsController();
        $pluginManager = $this->controller->getPluginManager();
        $plugin = $this
            ->getMockBuilder('ScContent\Controller\Plugin\TranslatorProxy')
            ->disableOriginalConstructor()
            ->setMethods(['__invoke'])
            ->getMock();

        $pluginManager->setFactory(
            'scTranslate',
            function() use ($plugin) {
                return $plugin;
            }
        );

        $this->routeMatch = new RouteMatch([
            'controller' => 'ScController.Installation.Requirements',
        ]);
        $this->request = new Request();
        $this->event = new MvcEvent();

        $config = $serviceManager->get('Config');
        $routerConfig = isset($config['router']) ? $config['router'] : [];
        $router = HttpRouter::factory($routerConfig);

        $this->event->setRouter($router);
        $this->event->setRouteMatch($this->routeMatch);
        $this->controller->setEvent($this->event);
        $this->controller->setServiceLocator($serviceManager);
    }

    /**
     * @covers ::configurationAction
     */
    public function testConfigurationActionWhenValidatorIsNotInstanceOf_PhpIni()
    {
        $config = [
            'steps' => [
                'test_step' => [
                    'chain' => [
                        'test_member' => [
                            'validator'  => 'fake_validator_name',
                            'controller' => 'fake_controller_name',
                        ],
                    ],
                ],
            ],
        ];
        $installation = InstallationOptionsFactory::make('test', $config);
        $installation->setCurrentStepName('test_step');
        $installation->getStep('test_step')->setCurrentMemberName('test_member');
        $this->controller->setInstallation($installation);

        $validator = $this->getMockForAbstractClass(
            'Zend\Validator\AbstractValidator'
        );
        $this->controller->setValidator($validator);

        $this->routeMatch->setParam('action', 'configuration');

        $this->setExpectedException('ScContent\Exception\DomainException');
        $this->controller->dispatch($this->request);
    }

    /**
     * @covers ::configurationAction
     */
    public function testConfigurationActionWithoutValidationFailures()
    {
        $config = [
            'steps' => [
                'test_step' => [
                    'chain' => [
                        'test_member' => [
                            'validator'  => 'fake_validator_name',
                            'controller' => 'fake_controller_name',
                            'batch' => [
                                [
                                    'name'             => 'fake_php.ini_param_name',
                                    'validation_type'  => 'fake_validation_type',
                                    'validation_value' => 'fake_validation_value',
                                    'failure_message'  => 'fake_failure_message',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
        $installation = InstallationOptionsFactory::make('test', $config);
        $installation->setCurrentStepName('test_step');
        $installation->getStep('test_step')->setCurrentMemberName('test_member');
        $this->controller->setInstallation($installation);

        $validator = $this->getMock('ScContent\Validator\Installation\PhpIni');
        $this->controller->setValidator($validator);
        $validator->expects($this->once())
            ->method('isValid')
            ->will($this->returnValue(true));

        $this->routeMatch->setParam('action', 'configuration');

        $result   = $this->controller->dispatch($this->request);
        $response = $this->controller->getResponse();

        $this->assertEquals(302, $response->getStatusCode());
    }

    /**
     * @covers ::configurationAction
     */
    public function testConfigurationActionWhenMissingOption_failure_message()
    {
        $config = [
            'steps' => [
                'test_step' => [
                    'chain' => [
                        'test_member' => [
                            'validator'  => 'fake_validator_name',
                            'controller' => 'fake_controller_name',
                            'batch' => [
                                [
                                    'name'             => 'fake_php.ini_param_name',
                                    'validation_type'  => 'fake_validation_type',
                                    'validation_value' => 'fake_validation_value',
                                    // missing 'failure_message' => '...',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
        $installation = InstallationOptionsFactory::make('test', $config);
        $installation->setCurrentStepName('test_step');
        $installation->getStep('test_step')->setCurrentMemberName('test_member');
        $this->controller->setInstallation($installation);

        $validator = $this->getMock('ScContent\Validator\Installation\PhpIni');
        $this->controller->setValidator($validator);
        $validator->expects($this->once())
            ->method('isValid')
            ->will($this->returnValue(false));

        $this->routeMatch->setParam('action', 'configuration');

        $this->setExpectedException(
            'ScContent\Exception\InvalidArgumentException'
        );
        $this->controller->dispatch($this->request);
    }

    /**
     * @covers ::configurationAction
     */
    public function testConfigurationActionWithValidationFailures()
    {
        $config = [
            'steps' => [
                'test_step' => [
                    'chain' => [
                        'test_member' => [
                            'validator'  => 'fake_validator_name',
                            'controller' => 'fake_controller_name',
                            'batch' => [
                                [
                                    'name'             => 'fake_php.ini_param_name',
                                    'validation_type'  => 'fake_validation_type',
                                    'validation_value' => 'fake_validation_value',
                                    'failure_message'  => 'fake_failure_message',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
        $installation = InstallationOptionsFactory::make('test', $config);
        $installation->setCurrentStepName('test_step');
        $installation->getStep('test_step')->setCurrentMemberName('test_member');
        $this->controller->setInstallation($installation);

        $validator = $this->getMock('ScContent\Validator\Installation\PhpIni');
        $this->controller->setValidator($validator);
        $validator->expects($this->once())
            ->method('isValid')
            ->will($this->returnValue(false));

        $this->routeMatch->setParam('action', 'configuration');

        $result   = $this->controller->dispatch($this->request);
        $response = $this->controller->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * @covers ::extensionAction
     */
    public function testExtensionActionWhenValidatorIsNotInstanceOf_PhpExtension()
    {
        $config = [
            'steps' => [
                'test_step' => [
                    'chain' => [
                        'test_member' => [
                            'validator'  => 'fake_validator_name',
                            'controller' => 'fake_controller_name',
                        ],
                    ],
                ],
            ],
        ];
        $installation = InstallationOptionsFactory::make('test', $config);
        $installation->setCurrentStepName('test_step');
        $installation->getStep('test_step')->setCurrentMemberName('test_member');
        $this->controller->setInstallation($installation);

        $validator = $this->getMockForAbstractClass(
            'Zend\Validator\AbstractValidator'
        );
        $this->controller->setValidator($validator);

        $this->routeMatch->setParam('action', 'extension');

        $this->setExpectedException('ScContent\Exception\DomainException');
        $this->controller->dispatch($this->request);
    }

    /**
     * @covers ::extensionAction
     */
    public function testExtensionActionWithoutValidationFailures()
    {
        $config = [
            'steps' => [
                'test_step' => [
                    'chain' => [
                        'test_member' => [
                            'validator'  => 'fake_validator_name',
                            'controller' => 'fake_controller_name',
                            'batch' => [
                                [
                                    'name'        => 'fake_php_extension_name',
                                    'information' => 'fake_information',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
        $installation = InstallationOptionsFactory::make('test', $config);
        $installation->setCurrentStepName('test_step');
        $installation->getStep('test_step')->setCurrentMemberName('test_member');
        $this->controller->setInstallation($installation);

        $validator = $this->getMock('ScContent\Validator\Installation\PhpExtension');
        $this->controller->setValidator($validator);
        $validator->expects($this->once())
            ->method('isValid')
            ->will($this->returnValue(true));

        $this->routeMatch->setParam('action', 'extension');

        $result   = $this->controller->dispatch($this->request);
        $response = $this->controller->getResponse();

        $this->assertEquals(302, $response->getStatusCode());
    }

    /**
     * @covers ::extensionAction
     */
    public function testExtensionActionWhenMissingOption_information()
    {
        $config = [
            'steps' => [
                'test_step' => [
                    'chain' => [
                        'test_member' => [
                            'validator'  => 'fake_validator_name',
                            'controller' => 'fake_controller_name',
                            'batch' => [
                                [
                                    'name' => 'fake_php_extension_name',
                                    // missing 'information' => '...',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
        $installation = InstallationOptionsFactory::make('test', $config);
        $installation->setCurrentStepName('test_step');
        $installation->getStep('test_step')->setCurrentMemberName('test_member');
        $this->controller->setInstallation($installation);

        $validator = $this->getMock('ScContent\Validator\Installation\PhpExtension');
        $this->controller->setValidator($validator);
        $validator->expects($this->once())
            ->method('isValid')
            ->will($this->returnValue(false));

        $this->routeMatch->setParam('action', 'extension');

        $this->setExpectedException(
            'ScContent\Exception\InvalidArgumentException'
        );
        $this->controller->dispatch($this->request);
    }

    /**
     * @covers ::extensionAction
     */
    public function testExtensionActionWithValidationFailures()
    {
        $config = [
            'steps' => [
                'test_step' => [
                    'chain' => [
                        'test_member' => [
                            'validator'  => 'fake_validator_name',
                            'controller' => 'fake_controller_name',
                            'batch' => [
                                [
                                    'name'        => 'fake_php_extension_name',
                                    'information' => 'fake_information',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
        $installation = InstallationOptionsFactory::make('test', $config);
        $installation->setCurrentStepName('test_step');
        $installation->getStep('test_step')->setCurrentMemberName('test_member');
        $this->controller->setInstallation($installation);

        $validator = $this->getMock('ScContent\Validator\Installation\PhpExtension');
        $this->controller->setValidator($validator);
        $validator->expects($this->once())
            ->method('isValid')
            ->will($this->returnValue(false));

        $this->routeMatch->setParam('action', 'extension');

        $result   = $this->controller->dispatch($this->request);
        $response = $this->controller->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
    }
}
