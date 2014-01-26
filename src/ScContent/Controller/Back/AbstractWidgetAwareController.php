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

use ScContent\Service\Back\WidgetConfigurationService,
    ScContent\Controller\AbstractBack,
    ScContent\Exception\RuntimeException;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
abstract class AbstractWidgetAwareController extends AbstractBack
{
    /**
     * @var ScContent\Service\Back\WidgetConfigurationService
     */
    protected $widgetConfigurationService;

    /**
     * @param unknown $id
     * @return NULL | ScContent\Entity\WidgetInterface
     */
    protected function deriveWidget($id)
    {
        $service = $this->getWidgetConfigurationService();
        try {
            $widget = $service->findWidget($id);
        } catch (RuntimeException $e) {
            $this->flashMessenger()->addMessage($e->getMessage());
            $this->redirect()
                ->toRoute('sc-admin/themes')
                ->setStatusCode(303);

            return null;
        }
        if ($widget->findOption('immutable')) {
            $this->flashMessenger()->addMessage(sprintf(
                $this->scTranslate(
                    "The widget '%s' is immutable."
                ),
                $widget->getDisplayName()
            ));
            $this->redirect()
                ->toRoute('sc-admin/layout', ['theme' => $widget->getTheme()])
                ->setStatusCode(303);

            return null;
        }
        return $widget;
    }

    /**
     * @param ScContent\Service\Back\WidgetConfigurationService $service
     * @return void
     */
    public function setWidgetConfigurationService(WidgetConfigurationService $service)
    {
        $this->widgetConfigurationService = $service;
    }

    /**
     * @return ScContent\Service\Back\WidgetConfigurationService
     */
    public function getWidgetConfigurationService()
    {
        if (! $this->widgetConfigurationService instanceof WidgetConfigurationService) {
            $serviceLocator = $this->getServiceLocator();
            $this->widgetConfigurationService = $serviceLocator->get(
                'ScService.Back.WidgetConfiguration'
            );
        }
        return $this->widgetConfigurationService;
    }
}
