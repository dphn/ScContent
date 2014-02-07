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
                ->toRoute('sc-admin/layout')
                ->setStatusCode(303);
        }

        $event = $this->getRequest()->getPost('suboperation');
        if (! empty($event)) {
            $events = $this->getEventManager();
            $params = $this->request->getPost();
            $params['theme'] = $theme->getName();
            $results = $events->trigger($event, $this, $params);
            foreach ($results as $result) {
                if ($result instanceof Response) {
                    return $result;
                }
            }
        }

        $view = new ViewModel([
            'controlSet' => $service->getControlSet(),
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
    * @return Zend\Stdlib\ResponseInterface
    */
    public function addAction()
    {
        $theme = $this->params()->fromRoute('theme');
        if (! $theme) {
            $this->flashMessenger()->addMessage(
                $this->scTranslate(
                    'Unable to add a widget. Theme is not specified.'
                )
            );
            return $this->redirect()
                ->toRoute('sc-admin/layout')
                ->setStatusCode(303);
        }

        $widgetName = $this->params()->fromRoute('name');
        if (! $widgetName) {
            $this->flashMessenger()->addMessage(
                $this->scTranslate(
                    'Unable to add a widget. Widget name is not specified.'
                )
            );
            return $this->redirect()
                ->toRoute(
                    'sc-admin/layout/index',
                    ['theme' => $theme]
                )
                ->setStatusCode(303);
        }

        $service = $this->getLayoutService();
        try {
            $widget = $service->addWidget($theme, $widgetName);
            return $this->redirect()
                ->toRoute(
                    'sc-admin/widget/configure',
                    ['id' => $widget->getId()]
                );
        } catch (RuntimeException $e) {
            $this->flashMessenger()->addMessage($e->getMessage());
            return $this->redirect()
                ->toRoute(
                    'sc-admin/layout/index',
                    ['theme' => $theme]
                )
                ->setStatusCode(303);
        }
    }

    /**
     * @return Zend\Stdlib\ResponseInterface
     */
    public function deleteAction()
    {
        $id = $this->params()->fromRoute('id');
        if (! is_numeric($id)) {
            $this->flashMessenger()->addMessage(
                $this->scTranslate('The widget identifier was not specified.')
            );
            return $this->redirect()
                ->toRoute('sc-admin/layout')
                ->setStatusCode(303);
        }

        $service = $this->getLayoutService();
        $service->deleteWidget($id);

        return $this->redirect()
            ->toRoute(
                'sc-admin/layout/index',
                ['theme' => $this->params()->fromRoute('theme')]
            );
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
