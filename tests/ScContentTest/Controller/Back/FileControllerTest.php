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

use ScContent\Controller\Back\FileController,
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
class FileControllerTest extends PHPUnit_Framework_TestCase
{
    protected $controller;
    protected $routeMatch;
    protected $response;
    protected $request;
    protected $event;

    protected $fakeAddFileForm;
    protected $fakeEditFileForm;
    protected $fakeFileService;
    protected $fakeFilesList;
    protected $fakeFileTransfer;

    protected function setUp()
    {
        $serviceManager = Bootstrap::getServiceManager();
        $this->controller = new FileController();

        $this->fakeAddFileForm = $this
            ->getMockBuilder('ScContent\Form\Back\FileAdd')
            ->disableOriginalConstructor()
            ->setMethods(array('setData', 'isValid', 'getData'))
            ->getMock();

        $this->controller->setAddFileForm($this->fakeAddFileForm);

        $this->fakeEditFileForm = $this
            ->getMockBuilder('ScContent\Form\Back\FileEdit')
            ->disableOriginalConstructor()
            ->setMethods(array('bind', 'isValid'))
            ->getMock();

        $this->controller->setEditFileForm($this->fakeEditFileForm);

        $this->fakeFileService = $this
            ->getMockBuilder('ScContent\Service\Back\FileService')
            ->setMethods(array('getFilesList', 'makeFiles', 'saveFiles'))
            ->getMock();

        $this->controller->setFileService($this->fakeFileService);

        $this->fakeFileTransfer = $this
            ->getMockBuilder('ScContent\Service\FileTransfer')
            ->disableOriginalConstructor()
            ->setMethods(array('receive', 'rollBack'))
            ->getMock();

        $this->controller->setFileTransfer($this->fakeFileTransfer);

        $this->fakeFilesList = $this
            ->getMockBuilder('ScContent\Entity\Back\FilesList')
            ->setMethods(array('isEmpty'))
            ->getMock();

        $this->routeMatch = new RouteMatch(array('controller' => 'sc-controller.back.article'));
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
     * @covers FileController::add
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

        $this->fakeFileService->expects($this->once())
            ->method('makeFiles')
            ->will($this->throwException($exception));

        $this->fakeAddFileForm->expects($this->once())
            ->method('isValid')
            ->will($this->returnValue(true));

        $this->fakeAddFileForm->expects($this->once())
            ->method('getData')
            ->will($this->returnValue(array()));

        $this->fakeFileTransfer->expects($this->once())
            ->method('receive')
            ->will($this->returnValue(array()));

        $this->fakeFileTransfer->expects($this->once())
            ->method('rollBack');

        $this->routeMatch->setParam('action', 'add');
        $this->routeMatch->setParam('parent', 1);
        $this->routeMatch->setParam('files', array());
        $this->request->setMethod(Request::METHOD_POST);

        $result   = $this->controller->dispatch($this->request);
        $response = $this->controller->getResponse();

        $this->assertEquals(303, $response->getStatusCode());
    }

    /**
     * @covers FileController::add
     */
    public function testAddActionSuccess()
    {
        $this->routeMatch->setParam('action', 'add');
        $this->routeMatch->setParam('parent', 1);

        $result   = $this->controller->dispatch($this->request);
        $response = $this->controller->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * @covers FileController::edit
     */
    public function testEditActionWithoutFileIdentifiers()
    {
        $this->routeMatch->setParam('action', 'edit');

        $result   = $this->controller->dispatch($this->request);
        $response = $this->controller->getResponse();

        $this->assertEquals(303, $response->getStatusCode());
    }

    /**
     * @covers FileController::edit
     */
    public function testEditActionWithEmptyFilesList()
    {
        $this->fakeFilesList->expects($this->once())
            ->method('isEmpty')
            ->will($this->returnValue(true));

        $this->fakeFileService->expects($this->once())
            ->method('getFilesList')
            ->will($this->returnValue($this->fakeFilesList));

        $this->routeMatch->setParam('action', 'edit');
        $this->routeMatch->setParam('id', 1);

        $result   = $this->controller->dispatch($this->request);
        $response = $this->controller->getResponse();

        $this->assertEquals(303, $response->getStatusCode());
    }

    /**
     * @covers FileController::edit
     */
    public function testEditActionSuccess()
    {
        $this->fakeFilesList->expects($this->once())
            ->method('isEmpty')
            ->will($this->returnValue(false));

        $this->fakeFileService->expects($this->once())
            ->method('getFilesList')
            ->will($this->returnValue($this->fakeFilesList));

        $this->fakeFileService->expects($this->once())
            ->method('saveFiles');

        $this->fakeEditFileForm->expects($this->once())
            ->method('isValid')
            ->will($this->returnValue(true));

        $this->fakeEditFileForm->expects($this->once())
            ->method('bind');

        $this->request->setMethod(Request::METHOD_POST);

        $this->routeMatch->setParam('action', 'edit');
        $this->routeMatch->setParam('id', 1);

        $result   = $this->controller->dispatch($this->request);
        $response = $this->controller->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
    }
}
