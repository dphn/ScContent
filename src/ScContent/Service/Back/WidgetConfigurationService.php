<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013-2014 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Service\Back;

use ScContent\Service\AbstractService,
    ScContent\Mapper\Back\WidgetMapper,
    ScContent\Entity\Back\WidgetConfig,
    ScContent\Entity\WidgetInterface,
    ScContent\Entity\Widget,
    ScContent\Mapper\RolesMapper,
    //
    ScContent\Exception\RuntimeException,
    ScContent\Exception\DebugException,
    ScContent\Exception\IoCException,
    //
    ScContent\Mapper\Exception\UnavailableSourceException,
    //
    Exception;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class WidgetConfigurationService extends AbstractService
{
    /**
     * @var \ScContent\Mapper\Back\WidgetMapper
     */
    protected $widgetMapper;

    /**
     * @var \ScContent\Mapper\RolesMapper
     */
    protected $rolesMapper;

    /**
     * @param  \ScContent\Mapper\Back\WidgetMapper $mapper
     * @return void
     */
    public function setWidgetMapper(WidgetMapper $mapper)
    {
        $this->widgetMapper = $mapper;
    }

    /**
     * @throws \ScContent\Exception\IoCException
     * @return \ScContent\Mapper\Back\WidgetMapper
     */
    public function getWidgetMapper()
    {
        if (! $this->widgetMapper instanceof WidgetMapper) {
            throw new IoCException(
                'The widget mapper was not set.'
            );
        }
        return $this->widgetMapper;
    }

    /**
     * @param  \ScContent\Mapper\RolesMapper $mapper
     * @return void
     */
    public function setRolesMapper(RolesMapper $mapper)
    {
        $this->rolesMapper = $mapper;
    }

    /**
     * @throws \ScContent\Exception\IoCException
     * @return \ScContent\Mapper\RolesMapper
     */
    public function getRolesMapper()
    {
        if (! $this->rolesMapper instanceof RolesMapper) {
            throw new IoCException(
                'The roles mapper was not set.'
            );
        }
        return $this->rolesMapper;
    }

    /**
     * @param  \ScContent\Entity\WidgetInterface $widget
     * @return \ScContent\Entity\Back\WidgetConfig
     */
    public function getWidgetConfig(WidgetInterface $widget)
    {
        $rolesMapper = $this->getRolesMapper();
        $availableRoles = $rolesMapper->findRegisteredRoles();

        $widgetConfig = new WidgetConfig($widget, $availableRoles);
        return $widgetConfig;
    }

    /**
     * @param  integer $id
     * @throws \ScContent\Exception\RuntimeException
     * @throws \ScContent\Exception\DebugException
     * @return \ScContent\Entity\Widget
     */
    public function findWidget($id)
    {
        $translator = $this->getTranslator();
        $widgetMapper = $this->getWidgetMapper();

        $widget = new Widget();
        $widget->setId($id);
        try {
            $widgetMapper->find($widget);
            return $widget;
        } catch (UnavailableSourceException $e) {
            throw new RuntimeException(sprintf(
                $translator->translate(
                    "Widget with identifier '%s' was not found."
                ),
                $id
            ));
        } catch (Exception $e) {
            if (DEBUG_MODE) {
                throw new DebugException(
                    $translator->translate('Error: ') . $e->getMessage(),
                    $e->getCode(),
                    $e
                );
            }
            throw new RuntimeException(sprintf(
                $translator->translate(
                    "An unexpected error occurred during the search widget with id '%s'."
                ),
                $id
            ));
        }
    }

    /**
     * @param  \ScContent\Entity\WidgetInterface $widget
     * @return void
     */
    public function saveWidget(WidgetInterface $widget)
    {
        $translator = $this->getTranslator();
        $mapper = $this->getWidgetMapper();
        try {
            $mapper->save($widget);
        } catch (Exception $e) {
            if (DEBUG_MODE) {
                throw new DebugException(
                    $translator->translate('Error: ') . $e->getMessage(),
                    $e->getCode(),
                    $e
                );
            }
            throw new RuntimeException($translator->translate(
                'Failed to save widget. An unexpected error occurred.'
            ));
        }
    }
}
