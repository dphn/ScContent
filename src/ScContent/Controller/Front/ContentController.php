<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013-2014 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Controller\Front;

use ScContent\Controller\AbstractFront,
    //
    Zend\View\Model\ViewModel;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class ContentController extends AbstractFront
{
    /**
     * @var ScContent\Service\Front\ContentService
     */
    protected $contentService;

    /**
     * @return Zend\View\Model\ViewModel | Zend\Stdlib\ResponseInterface
     */
    public function indexAction()
    {
        $service = $this->getContentService();
        $contentName = $this->params()->fromRoute('content-name');
        $serviceLocator = $this->getServiceLocator();

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
