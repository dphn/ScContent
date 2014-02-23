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
    protected $routeMatchPrototype;
    protected $request;
    protected $event;

    protected $fakeInstallationInspectror;

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

        $this->fakeInstallationInspectror = $this
            ->getMockBuilder('ScContent\Listener\Installation\InstallationInspector')
            ->setMethods(['getCurrentSetup'])
            ->getMock();

        $this->controller->setInstallationInspector(
            $this->fakeInstallationInspectror
        );

        $this->routeMatchPrototype = new RouteMatch([
            'controller' => 'ScController.Installation.Requirements',
        ]);
        $this->request = new Request();
        $this->event = new MvcEvent();

        $config = $serviceManager->get('Config');
        $routerConfig = isset($config['router']) ? $config['router'] : [];
        $router = HttpRouter::factory($routerConfig);

        $this->event->setRouter($router);
        $this->controller->setEvent($this->event);
        $this->controller->setServiceLocator($serviceManager);
    }

    /**
     * @covers ::configurationAction
     */
    public function testEmpty()
    {

    }
}
