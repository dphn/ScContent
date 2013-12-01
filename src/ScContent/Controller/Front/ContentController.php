<?php
namespace ScContent\Controller\Front;

use ScContent\Controller\AbstractFront,
    //
    Zend\View\Model\ViewModel;

class ContentController extends AbstractFront
{
    /**
     * @var ScContent\Service\Front\ContentService
     */
    protected $contentService;

    /**
     * @return
     */
    public function indexAction()
    {
        $service = $this->getContentService();
        $contentName = $this->params()->fromRoute('content-name');
        try {
            $content = $service->getContent($contentName);
        } catch (\Exception $e) {
            $this->getResponse()->setStatusCode(404);
            return;
        }
        return new ViewModel([
            'content' => $content,
        ]);
    }

    /**
     * @param ScContent\Service\Front\ContentService $service
     * @return void
     */
    public function setContentService($service)
    {
        $this->contentService = $service;
    }

    /**
     * @return ScContent\Service\Front\ContentService
     */
    public function getContentService()
    {
        if (! $this->contentService instanceof ContentService) {
            $serviceLocator = $this->getServiceLocator();
            $this->contentService = $serviceLocator->get(
        	   'ScService.Front.ContentService'
            );
        }
        return $this->contentService;
    }
}
