<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Controller\Back;

use ScContent\Service\Back\GarbageCollector,
    ScContent\Service\Stdlib,
    //
    Zend\Mvc\Controller\AbstractActionController;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class GarbageController extends AbstractActionController
{
    /**
     * @var ScContent\Service\Back\GarbageCollector
     */
    protected $garbageCollector;

    /**
     * Collects garbage - removes files from the file system.
     *
     * @return Zend\Stdlib\ResponseInterface
     */
    public function collectAction()
    {
        // @todo json
        $service = $this->getGarbageCollector();
        if ($service->collect()) {
            return $this->redirect()->toRoute('sc-admin/content-manager');
        }
        return $this->redirect()
            ->toRoute(
                'sc-admin/file/delete',
                ['random' => Stdlib::randomKey(6)]
            )
            ->setStatusCode(303);
    }

    /**
     * @param ScContent\Service\Back\GarbageCollector $service
     * @return void
     */
    public function setGarbageCollector(GarbageCollector $service)
    {
        $this->garbageCollector = $service;
    }

    /**
     * @return ScContent\Service\Back\GarbageCollector
     */
    public function getGarbageCollector()
    {
        if (! $this->garbageCollector instanceof GarbageCollector) {
            $serviceLocator = $this->getServiceLocator();
            $this->garbageCollector = $serviceLocator->get(
                'ScService.Back.GarbageCollector'
            );
        }
        return $this->garbageCollector;
    }
}
