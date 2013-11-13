<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContentTest\Controller\Back;

use ScContent\Controller\Back\ArticleController,
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
class ArticleControllerTest extends PHPUnit_Framework_TestCase
{
    protected $controller;
    protected $routeMatch;
    protected $response;
    protected $request;
    protected $event;

    protected $fakeForm;
    protected $fakeArticleService;

    protected function setUp()
    {
        $serviceManager = Bootstrap::getServiceManager();
        $this->controller = new ArticleController();

        $this->fakeForm = $this
            ->getMockBuilder('ScContent\Form\Back\Article')
            ->disableOriginalConstructor()
            ->setMethods(array('bind', 'isValid', 'getData'))
            ->getMock();

        $this->controller->setArticleForm($this->fakeForm);

        $this->fakeArticleService = $this
            ->getMockBuilder('ScContent\Service\Back\ArticleService')
            ->setMethods(array('makeArticle', 'getArticle', 'saveContent'))
            ->getMock();

        $this->controller->setArticleService($this->fakeArticleService);

        $this->routeMatch = new RouteMatch(array(
            'controller' => 'sc-controller.back.article',
        ));
        $this->request = new Request();
        $this->event = new MvcEvent();

        $config = $serviceManager->get('Config');
        $routerConfig = isset($config['router']) ? $config['router'] : array();
        $router = HttpRouter::factory($routerConfig);

        $this->event->setRouter($router);
        $this->event->setRouteMatch($this->routeMatch);
        $this->controller->setEvent($this->event);
        $this->controller->setServiceLocator($serviceManager);
    }

    /**
     * @covers ArticleController::add
     */
    public function testAddActionWithoutParentIdentifier()
    {
        $this->routeMatch->setParam('action', 'add');

        $result   = $this->controller->dispatch($this->request);
        $response = $this->controller->getResponse();

        $this->assertEquals(303, $response->getStatusCode());
    }

    /**
     * @covers ArticleController::add
     */
    public function testAddActionOnCreationError()
    {
        $exception = new RuntimeException();

        $this->fakeArticleService->expects($this->once())
            ->method('makeArticle')
            ->will($this->throwException($exception));

        $this->routeMatch->setParam('action', 'add');
        $this->routeMatch->setParam('parent', 1);

        $result   = $this->controller->dispatch($this->request);
        $response = $this->controller->getResponse();

        $this->assertEquals(303, $response->getStatusCode());
    }

    /**
     * @covers ArticleController::add
     */
    public function testAddActionSuccess()
    {
        $this->fakeArticleService->expects($this->once())
            ->method('makeArticle')
            ->will($this->returnValue(1));

        $this->routeMatch->setParam('action', 'add');
        $this->routeMatch->setParam('parent', 1);

        $result   = $this->controller->dispatch($this->request);
        $response = $this->controller->getResponse();

        $this->assertEquals(302, $response->getStatusCode());
    }

    /**
     * @covers ArticleController::edit
     */
    public function testEditActionWithoutArticleIdentifier()
    {
        $this->routeMatch->setParam('action', 'edit');

        $result   = $this->controller->dispatch($this->request);
        $response = $this->controller->getResponse();

        $this->assertEquals(303, $response->getStatusCode());
    }

    /**
     * @covers ArticleController::edit
     */
    public function testEditActionWithBadArticleIdentifier()
    {
        $exception = new RuntimeException();

        $this->fakeArticleService->expects($this->once())
            ->method('getArticle')
            ->will($this->throwException($exception));

        $this->routeMatch->setParam('action', 'edit');
        $this->routeMatch->setParam('id', 1);

        $result   = $this->controller->dispatch($this->request);
        $response = $this->controller->getResponse();

        $this->assertEquals(303, $response->getStatusCode());
    }

    /**
     * @covers ArticleController::edit
     */
    public function testEditActionSuccess()
    {
        $fakeArticle = $this->getMock('ScContent\Entity\Back\Article');

        $this->fakeArticleService->expects($this->once())
            ->method('getArticle')
            ->will($this->returnValue($fakeArticle));

        $this->fakeArticleService->expects($this->once())
            ->method('saveContent');

        $this->fakeForm->expects($this->once())
            ->method('bind');

        $this->fakeForm->expects($this->once())
            ->method('isValid')
            ->will($this->returnValue(true));

        $this->fakeForm->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($fakeArticle));

        $this->request->setMethod(Request::METHOD_POST);

        $this->routeMatch->setParam('action', 'edit');
        $this->routeMatch->setParam('id', 1);

        $result   = $this->controller->dispatch($this->request);
        $response = $this->controller->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
    }
}
