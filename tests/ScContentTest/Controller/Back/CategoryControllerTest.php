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

use ScContent\Controller\Back\CategoryController,
    ScContent\Exception\RuntimeException,
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
 */
class CategoryControllerTest extends PHPUnit_Framework_TestCase
{
    protected $controller;
    protected $routeMatch;
    protected $response;
    protected $request;
    protected $event;

    protected $fakeForm;
    protected $fakeCategoryService;

    protected function setUp()
    {
        $serviceManager = Bootstrap::getServiceManager();
        $this->controller = new CategoryController();
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

        $this->fakeForm = $this
            ->getMockBuilder('ScContent\Form\Back\CategoryForm')
            ->disableOriginalConstructor()
            ->setMethods(['bind', 'isValid', 'getData'])
            ->getMock();

        $this->controller->setCategoryForm($this->fakeForm);

        $this->fakeCategoryService = $this
            ->getMockBuilder('ScContent\Service\Back\CategoryService')
            ->setMethods(['makeCategory', 'getCategory', 'saveContent'])
            ->getMock();

        $this->controller->setCategoryService($this->fakeCategoryService);

        $this->routeMatch = new RouteMatch([
            'controller' => 'ScController.Back.Category',
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
     * @covers CategoryController::add
     */
    public function testAddActionWithoutParentIdentifier()
    {
        $this->routeMatch->setParam('action', 'add');

        $result   = $this->controller->dispatch($this->request);
        $response = $this->controller->getResponse();

        $this->assertEquals(303, $response->getStatusCode());
    }

    /**
     * @covers CategoryController::add
     */
    public function testAddActionOnCreationError()
    {
        $exception = new RuntimeException();

        $this->fakeCategoryService->expects($this->once())
            ->method('makeCategory')
            ->will($this->throwException($exception));

        $this->routeMatch->setParam('action', 'add');
        $this->routeMatch->setParam('parent', 1);

        $result   = $this->controller->dispatch($this->request);
        $response = $this->controller->getResponse();

        $this->assertEquals(303, $response->getStatusCode());
    }

    /**
     * @covers CategoryController::add
     */
    public function testAddActionSuccess()
    {
        $this->fakeCategoryService->expects($this->once())
            ->method('makeCategory')
            ->will($this->returnValue(1));

        $this->routeMatch->setParam('action', 'add');
        $this->routeMatch->setParam('parent', 1);

        $result   = $this->controller->dispatch($this->request);
        $response = $this->controller->getResponse();

        $this->assertEquals(302, $response->getStatusCode());
    }

    /**
     * @covers CategoryController::edit
     */
    public function testEditActionWithoutCategoryIdentifier()
    {
        $this->routeMatch->setParam('action', 'edit');

        $result   = $this->controller->dispatch($this->request);
        $response = $this->controller->getResponse();

        $this->assertEquals(303, $response->getStatusCode());
    }

    /**
     * @covers CategoryController::edit
     */
    public function testEditActionWithBadCategoryIdentifier()
    {
        $exception = new RuntimeException();

        $this->fakeCategoryService->expects($this->once())
            ->method('getCategory')
            ->will($this->throwException($exception));

        $this->routeMatch->setParam('action', 'edit');
        $this->routeMatch->setParam('id', 1);

        $result   = $this->controller->dispatch($this->request);
        $response = $this->controller->getResponse();

        $this->assertEquals(303, $response->getStatusCode());
    }

    /**
     * @covers CategoryController::edit
     */
    public function testEditActionSuccess()
    {
        $fakeCategory = $this->getMock('ScContent\Entity\Back\Article');

        $this->fakeCategoryService->expects($this->once())
            ->method('getCategory')
            ->will($this->returnValue($fakeCategory));

        $this->fakeCategoryService->expects($this->once())
            ->method('saveContent');

        $this->fakeForm->expects($this->once())
            ->method('bind');

        $this->fakeForm->expects($this->once())
            ->method('isValid')
            ->will($this->returnValue(true));

        $this->fakeForm->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($fakeCategory));

        $this->request->setMethod(Request::METHOD_POST);
        $this->routeMatch->setParam('action', 'edit');
        $this->routeMatch->setParam('id', 1);

        $result   = $this->controller->dispatch($this->request);
        $response = $this->controller->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
    }
}
