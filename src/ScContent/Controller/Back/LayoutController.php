<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013-2014 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Controller\Back;

use ScContent\Controller\AbstractBack,
    ScContent\Service\Back\LayoutService,
    ScContent\Exception\RuntimeException,
    //
    Zend\View\Model\ViewModel,
    Zend\Http\Response;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class LayoutController extends AbstractBack
{
    /**
     * @var ScContent\Service\Back\LayoutService
     */
    protected $layoutService;

    /**
     * Shows a layout.
     *
     * @return Zend\Stdlib\ResponseInterface | Zend\View\Model\ViewModel
     */
    public function indexAction()
    {
        $service = $this->getLayoutService();
        try {
            $theme = $service->getTheme(
                $this->params()->fromRoute('theme')
            );
        } catch (RuntimeException $e) {
            $this->flashMessenger()->addMessage($e->getMessage());
            return $this->redirect()
                ->toRoute('sc-admin/themes')
                ->setStatusCode(303);
        }

        if ($this->getRequest()->isPost()) {
            $events = $this->getEventManager();
            $params = $this->request->getPost();
            $params['theme'] = $theme->getName();
            $results = $events->trigger(
                __FUNCTION__,
                $this,
                $params
            );
            foreach ($results as $result) {
                if ($result instanceof Response) {
                    return $result;
                }
            }
        }
        $view = new ViewModel([
            'regions' => $service->getRegions($theme->getName()),
            'theme' => $theme,
        ]);

        $flashMessenger = $this->flashMessenger();
        if ($flashMessenger->hasMessages()) {
            $view->messages = $flashMessenger->getMessages();
        }

        return $view;
    }

    /**
     * @param ScContent\Service\Back\LayoutService $service
     * @return void
     */
    public function setLayoutService(LayoutService $service)
    {
        $this->layoutService = $service;
    }

    /**
     * @return ScContent\Service\Back\LayoutService
     */
    public function getLayoutService()
    {
        if (! $this->layoutService instanceof LayoutService) {
            $serviceLocator = $this->getServiceLocator();
            $this->layoutService = $serviceLocator->get(
                'ScService.Back.Layout'
            );
        }
        return $this->layoutService;
    }
}
